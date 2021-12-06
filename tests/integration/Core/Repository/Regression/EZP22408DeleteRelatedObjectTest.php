<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Repository\Regression;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCreateStruct;
use Ibexa\Tests\Integration\Core\Repository\BaseTest;

class EZP22408DeleteRelatedObjectTest extends BaseTest
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType */
    private $testContentType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createTestContentType();
    }

    public function testRelationListIsUpdatedWhenRelatedObjectIsDeleted()
    {
        $targetObject1 = $this->createTargetObject('Relation list target object 1');
        $targetObject2 = $this->createTargetObject('Relation list target object 2');
        $referenceObject = $this->createReferenceObject(
            'Reference object',
            [
                $targetObject1->id,
                $targetObject2->id,
            ]
        );

        $contentService = $this->getRepository()->getContentService();
        $contentService->deleteContent($targetObject1->contentInfo);

        $reloadedReferenceObject = $contentService->loadContent($referenceObject->id);
        /** @var \Ibexa\Core\FieldType\RelationList\Value */
        $relationListValue = $reloadedReferenceObject->getFieldValue('relation_list');
        $this->assertSame([$targetObject2->id], $relationListValue->destinationContentIds);
    }

    public function testSingleRelationIsUpdatedWhenRelatedObjectIsDeleted()
    {
        $targetObject = $this->createTargetObject('Single relation target object');
        $referenceObject = $this->createReferenceObject(
            'Reference object',
            [],
            $targetObject->id
        );

        $contentService = $this->getRepository()->getContentService();
        $contentService->deleteContent($targetObject->contentInfo);

        $reloadedReferenceObject = $contentService->loadContent($referenceObject->id);
        /** @var \Ibexa\Core\FieldType\Relation\Value */
        $relationValue = $reloadedReferenceObject->getFieldValue('single_relation');
        $this->assertEmpty($relationValue->destinationContentId);
    }

    private function createTestContentType()
    {
        $languageCode = $this->getMainLanguageCode();
        $contentTypeService = $this->getRepository()->getContentTypeService();

        $createStruct = $contentTypeService->newContentTypeCreateStruct('test_content_type');
        $createStruct->mainLanguageCode = $languageCode;
        $createStruct->names = [$languageCode => 'Test Content Type'];
        $createStruct->nameSchema = '<name>';
        $createStruct->urlAliasSchema = '<name>';

        $createStruct->addFieldDefinition(
            new FieldDefinitionCreateStruct(
                [
                    'fieldTypeIdentifier' => 'ezstring',
                    'identifier' => 'name',
                    'names' => [$languageCode => 'Name'],
                    'position' => 1,
                ]
            )
        );

        $createStruct->addFieldDefinition(
            new FieldDefinitionCreateStruct(
                [
                    'fieldTypeIdentifier' => 'ezobjectrelationlist',
                    'identifier' => 'relation_list',
                    'names' => [$languageCode => 'Relation List'],
                    'position' => 2,
                ]
            )
        );

        $createStruct->addFieldDefinition(
            new FieldDefinitionCreateStruct(
                [
                    'fieldTypeIdentifier' => 'ezobjectrelation',
                    'identifier' => 'single_relation',
                    'names' => [$languageCode => 'Single Relation'],
                    'position' => 3,
                ]
            )
        );

        $contentGroup = $contentTypeService->loadContentTypeGroupByIdentifier('Content');
        $this->testContentType = $contentTypeService->createContentType($createStruct, [$contentGroup]);
        $contentTypeService->publishContentTypeDraft($this->testContentType);
    }

    private function getMainLanguageCode()
    {
        return $this->getRepository()->getContentLanguageService()->getDefaultLanguageCode();
    }

    /**
     * @param string $name
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Content
     */
    private function createTargetObject($name)
    {
        $contentService = $this->getRepository()->getContentService();
        $createStruct = $contentService->newContentCreateStruct(
            $this->testContentType,
            $this->getMainLanguageCode()
        );
        $createStruct->setField('name', $name);

        $object = $contentService->createContent(
            $createStruct,
            [
                $this->getLocationCreateStruct(),
            ]
        );

        return $contentService->publishVersion($object->versionInfo);
    }

    /**
     * @param string $name
     * @param array $relationListTarget Array of destination content ids
     * @param int $singleRelationTarget Content id
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Content
     */
    private function createReferenceObject($name, array $relationListTarget = [], $singleRelationTarget = null)
    {
        $contentService = $this->getRepository()->getContentService();
        $createStruct = $contentService->newContentCreateStruct(
            $this->testContentType,
            $this->getMainLanguageCode()
        );

        $createStruct->setField('name', $name);
        if (!empty($relationListTarget)) {
            $createStruct->setField('relation_list', $relationListTarget);
        }

        if ($singleRelationTarget) {
            $createStruct->setField('single_relation', $singleRelationTarget);
        }

        $object = $contentService->createContent(
            $createStruct,
            [
                $this->getLocationCreateStruct(),
            ]
        );

        return $contentService->publishVersion($object->versionInfo);
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\LocationCreateStruct
     */
    private function getLocationCreateStruct()
    {
        return $this->getRepository()->getLocationService()->newLocationCreateStruct(2);
    }
}

class_alias(EZP22408DeleteRelatedObjectTest::class, 'eZ\Publish\API\Repository\Tests\Regression\EZP22408DeleteRelatedObjectTest');
