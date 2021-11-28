<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository;

use function array_filter;
use Exception;
use Ibexa\Contracts\Core\Persistence\Content\Location\Handler as LocationHandler;
use Ibexa\Contracts\Core\Persistence\Content\Section as SPISection;
use Ibexa\Contracts\Core\Persistence\Content\Section\Handler as SectionHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException as APINotFoundException;
use Ibexa\Contracts\Core\Repository\PermissionCriterionResolver;
use Ibexa\Contracts\Core\Repository\Repository as RepositoryInterface;
use Ibexa\Contracts\Core\Repository\SectionService as SectionServiceInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd as CriterionLogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalNot as CriterionLogicalNot;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Subtree as CriterionSubtree;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Contracts\Core\Repository\Values\Content\SectionCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\SectionUpdateStruct;
use Ibexa\Core\Base\Exceptions\BadStateException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentValue;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;

/**
 * Section service, used for section operations.
 */
class SectionService implements SectionServiceInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    protected $repository;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    protected $permissionResolver;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionCriterionResolver */
    protected $permissionCriterionResolver;

    /** @var \Ibexa\Contracts\Core\Persistence\Content\Section\Handler */
    protected $sectionHandler;

    /** @var \Ibexa\Contracts\Core\Persistence\Content\Location\Handler */
    protected $locationHandler;

    /** @var array */
    protected $settings;

    /**
     * Setups service with reference to repository object that created it & corresponding handler.
     *
     * @param \Ibexa\Contracts\Core\Repository\Repository $repository
     * @param \Ibexa\Contracts\Core\Persistence\Content\Section\Handler $sectionHandler
     * @param \Ibexa\Contracts\Core\Persistence\Content\Location\Handler $locationHandler
     * @param \Ibexa\Contracts\Core\Repository\PermissionCriterionResolver $permissionCriterionResolver
     * @param array $settings
     */
    public function __construct(RepositoryInterface $repository, SectionHandler $sectionHandler, LocationHandler $locationHandler, PermissionCriterionResolver $permissionCriterionResolver, array $settings = [])
    {
        $this->repository = $repository;
        $this->sectionHandler = $sectionHandler;
        $this->locationHandler = $locationHandler;
        $this->permissionResolver = $repository->getPermissionResolver();
        $this->permissionCriterionResolver = $permissionCriterionResolver;
        // Union makes sure default settings are ignored if provided in argument
        $this->settings = $settings + [
            //'defaultSetting' => array(),
        ];
    }

    /**
     * Creates a new Section in the content repository.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException If the current user user is not allowed to create a section
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If the new identifier in $sectionCreateStruct already exists
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\SectionCreateStruct $sectionCreateStruct
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Section The newly created section
     */
    public function createSection(SectionCreateStruct $sectionCreateStruct): Section
    {
        if (!is_string($sectionCreateStruct->name) || empty($sectionCreateStruct->name)) {
            throw new InvalidArgumentValue('name', $sectionCreateStruct->name, 'SectionCreateStruct');
        }

        if (!is_string($sectionCreateStruct->identifier) || empty($sectionCreateStruct->identifier)) {
            throw new InvalidArgumentValue('identifier', $sectionCreateStruct->identifier, 'SectionCreateStruct');
        }

        if (!$this->permissionResolver->canUser('section', 'edit', $sectionCreateStruct)) {
            throw new UnauthorizedException('section', 'edit');
        }

        try {
            $existingSection = $this->sectionHandler->loadByIdentifier($sectionCreateStruct->identifier);
            if ($existingSection !== null) {
                throw new InvalidArgumentException('sectionCreateStruct', 'A Section with the specified identifier already exists');
            }
        } catch (APINotFoundException $e) {
            // Do nothing
        }

        $this->repository->beginTransaction();
        try {
            $spiSection = $this->sectionHandler->create(
                $sectionCreateStruct->name,
                $sectionCreateStruct->identifier
            );
            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        return $this->buildDomainSectionObject($spiSection);
    }

    /**
     * Updates the given section in the content repository.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException If the current user user is not allowed to create a section
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If the new identifier already exists (if set in the update struct)
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Section $section
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\SectionUpdateStruct $sectionUpdateStruct
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Section
     */
    public function updateSection(Section $section, SectionUpdateStruct $sectionUpdateStruct): Section
    {
        if ($sectionUpdateStruct->name !== null && !is_string($sectionUpdateStruct->name)) {
            throw new InvalidArgumentValue('name', $section->name, 'Section');
        }

        if ($sectionUpdateStruct->identifier !== null && !is_string($sectionUpdateStruct->identifier)) {
            throw new InvalidArgumentValue('identifier', $section->identifier, 'Section');
        }

        if (!$this->permissionResolver->canUser('section', 'edit', $section)) {
            throw new UnauthorizedException('section', 'edit');
        }

        if ($sectionUpdateStruct->identifier !== null) {
            try {
                $existingSection = $this->sectionHandler->loadByIdentifier($sectionUpdateStruct->identifier);

                // Allowing identifier update only for the same section
                if ($existingSection->id != $section->id) {
                    throw new InvalidArgumentException('sectionUpdateStruct', 'A Section with the specified identifier already exists');
                }
            } catch (APINotFoundException $e) {
                // Do nothing
            }
        }

        $loadedSection = $this->sectionHandler->load($section->id);

        $this->repository->beginTransaction();
        try {
            $spiSection = $this->sectionHandler->update(
                $loadedSection->id,
                $sectionUpdateStruct->name ?: $loadedSection->name,
                $sectionUpdateStruct->identifier ?: $loadedSection->identifier
            );
            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        return $this->buildDomainSectionObject($spiSection);
    }

    /**
     * Loads a Section from its id ($sectionId).
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException if section could not be found
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException If the current user user is not allowed to read a section
     *
     * @param int $sectionId
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Section
     */
    public function loadSection(int $sectionId): Section
    {
        $section = $this->buildDomainSectionObject(
            $this->sectionHandler->load($sectionId)
        );

        if (!$this->permissionResolver->canUser('section', 'view', $section)) {
            throw new UnauthorizedException('section', 'view');
        }

        return $section;
    }

    /**
     * Loads all sections, excluding the ones the current user is not allowed to read.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Section[]
     */
    public function loadSections(): iterable
    {
        $sections = array_map(function ($spiSection) {
            return $this->buildDomainSectionObject($spiSection);
        }, $this->sectionHandler->loadAll());

        return array_values(array_filter($sections, function ($section) {
            return $this->permissionResolver->canUser('section', 'view', $section);
        }));
    }

    /**
     * Loads a Section from its identifier ($sectionIdentifier).
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException if section could not be found
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException If the current user user is not allowed to read a section
     *
     * @param string $sectionIdentifier
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Section
     */
    public function loadSectionByIdentifier(string $sectionIdentifier): Section
    {
        if (empty($sectionIdentifier)) {
            throw new InvalidArgumentValue('sectionIdentifier', $sectionIdentifier);
        }

        $section = $this->buildDomainSectionObject(
            $this->sectionHandler->loadByIdentifier($sectionIdentifier)
        );

        if (!$this->permissionResolver->canUser('section', 'view', $section)) {
            throw new UnauthorizedException('section', 'view');
        }

        return $section;
    }

    /**
     * Counts the contents which $section is assigned to.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Section $section
     *
     * @return int
     *
     * @deprecated since 6.0
     */
    public function countAssignedContents(Section $section): int
    {
        return $this->sectionHandler->assignmentsCount($section->id);
    }

    /**
     * Returns true if the given section is assigned to contents, or used in role policies, or in role assignments.
     *
     * This does not check user permissions.
     *
     * @since 6.0
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Section $section
     *
     * @return bool
     */
    public function isSectionUsed(Section $section): bool
    {
        return $this->sectionHandler->assignmentsCount($section->id) > 0 ||
               $this->sectionHandler->policiesCount($section->id) > 0 ||
               $this->sectionHandler->countRoleAssignmentsUsingSection($section->id) > 0;
    }

    /**
     * Assigns the content to the given section
     * this method overrides the current assigned section.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException If user does not have access to view provided object
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Section $section
     */
    public function assignSection(ContentInfo $contentInfo, Section $section): void
    {
        $loadedContentInfo = $this->repository->getContentService()->loadContentInfo($contentInfo->id);
        $loadedSection = $this->loadSection($section->id);

        if (!$this->permissionResolver->canUser('section', 'assign', $loadedContentInfo, [$loadedSection])) {
            throw new UnauthorizedException(
                'section',
                'assign',
                [
                    'name' => $loadedSection->name,
                    'content-name' => $loadedContentInfo->name,
                ]
            );
        }

        $this->repository->beginTransaction();
        try {
            $this->sectionHandler->assign(
                $loadedSection->id,
                $loadedContentInfo->id
            );
            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }
    }

    /**
     * Assigns the subtree to the given section
     * this method overrides the current assigned section.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Section $section
     */
    public function assignSectionToSubtree(Location $location, Section $section): void
    {
        $loadedSubtree = $this->repository->getLocationService()->loadLocation($location->id);
        $loadedSection = $this->loadSection($section->id);

        /**
         * Check read access to whole source subtree.
         *
         * @var bool|\Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion
         */
        $sectionAssignCriterion = $this->permissionCriterionResolver->getPermissionsCriterion(
            'section',
            'assign',
            [$loadedSection]
        );
        if ($sectionAssignCriterion === false) {
            throw new UnauthorizedException('section', 'assign', [
                'name' => $loadedSection->name,
                'subtree' => $loadedSubtree->pathString,
            ]);
        } elseif ($sectionAssignCriterion !== true) {
            // Query if there are any content in subtree current user don't have access to
            $query = new Query(
                [
                    'limit' => 0,
                    'filter' => new CriterionLogicalAnd(
                        [
                            new CriterionSubtree($loadedSubtree->pathString),
                            new CriterionLogicalNot($sectionAssignCriterion),
                        ]
                    ),
                ]
            );

            $result = $this->repository->getSearchService()->findContent($query, [], false);
            if ($result->totalCount > 0) {
                throw new UnauthorizedException('section', 'assign', [
                    'name' => $loadedSection->name,
                    'subtree' => $loadedSubtree->pathString,
                ]);
            }
        }

        $this->repository->beginTransaction();
        try {
            $this->locationHandler->setSectionForSubtree(
                $loadedSubtree->id,
                $loadedSection->id
            );
            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }
    }

    /**
     * Deletes $section from content repository.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException If the specified section is not found
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException If the current user is not allowed to delete a section
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException If section can not be deleted
     *         because it is still assigned to some contents,
     *         or because it is still being used in policy limitations.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Section $section
     */
    public function deleteSection(Section $section): void
    {
        $loadedSection = $this->loadSection($section->id);

        if (!$this->permissionResolver->canUser('section', 'edit', $loadedSection)) {
            throw new UnauthorizedException('section', 'edit', ['sectionId' => $loadedSection->id]);
        }

        if ($this->sectionHandler->assignmentsCount($loadedSection->id) > 0) {
            throw new BadStateException('section', 'The Section still has content assigned');
        }

        if ($this->sectionHandler->policiesCount($loadedSection->id) > 0) {
            throw new BadStateException('section', 'the Section is still being used in Policy Limitations');
        }

        $this->repository->beginTransaction();
        try {
            $this->sectionHandler->delete($loadedSection->id);
            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }
    }

    /**
     * Instantiates a new SectionCreateStruct.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\SectionCreateStruct
     */
    public function newSectionCreateStruct(): SectionCreateStruct
    {
        return new SectionCreateStruct();
    }

    /**
     * Instantiates a new SectionUpdateStruct.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\SectionUpdateStruct
     */
    public function newSectionUpdateStruct(): SectionUpdateStruct
    {
        return new SectionUpdateStruct();
    }

    /**
     * Builds API Section object from provided SPI Section object.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\Section $spiSection
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Section
     */
    protected function buildDomainSectionObject(SPISection $spiSection)
    {
        return new Section(
            [
                'id' => $spiSection->id,
                'identifier' => $spiSection->identifier,
                'name' => $spiSection->name,
            ]
        );
    }
}

class_alias(SectionService::class, 'eZ\Publish\Core\Repository\SectionService');
