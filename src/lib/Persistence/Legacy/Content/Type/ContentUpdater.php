<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Persistence\Legacy\Content\Type;

use Ibexa\Contracts\Core\Persistence\Content\Type;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\ConverterRegistry as Registry;
use Ibexa\Core\Persistence\Legacy\Content\Gateway as ContentGateway;
use Ibexa\Core\Persistence\Legacy\Content\Mapper as ContentMapper;
use Ibexa\Core\Persistence\Legacy\Content\StorageHandler;

/**
 * Class to update content objects to a new type version.
 */
class ContentUpdater
{
    /**
     * Content gateway.
     *
     * @var \Ibexa\Core\Persistence\Legacy\Content\Gateway
     */
    protected $contentGateway;

    /**
     * FieldValue converter registry.
     *
     * @var \Ibexa\Core\Persistence\Legacy\Content\FieldValue\ConverterRegistry
     */
    protected $converterRegistry;

    /**
     * Storage handler.
     *
     * @var \Ibexa\Core\Persistence\Legacy\Content\StorageHandler
     */
    protected $storageHandler;

    /** @var \Ibexa\Core\Persistence\Legacy\Content\Mapper */
    protected $contentMapper;

    /**
     * Creates a new content updater.
     *
     * @param \Ibexa\Core\Persistence\Legacy\Content\Gateway $contentGateway
     * @param \Ibexa\Core\Persistence\Legacy\Content\FieldValue\ConverterRegistry $converterRegistry
     * @param \Ibexa\Core\Persistence\Legacy\Content\StorageHandler $storageHandler
     * @param \Ibexa\Core\Persistence\Legacy\Content\Mapper $contentMapper
     */
    public function __construct(
        ContentGateway $contentGateway,
        Registry $converterRegistry,
        StorageHandler $storageHandler,
        ContentMapper $contentMapper
    ) {
        $this->contentGateway = $contentGateway;
        $this->converterRegistry = $converterRegistry;
        $this->storageHandler = $storageHandler;
        $this->contentMapper = $contentMapper;
    }

    /**
     * Determines the necessary update actions.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\Type $fromType
     * @param \Ibexa\Contracts\Core\Persistence\Content\Type $toType
     *
     * @return \Ibexa\Core\Persistence\Legacy\Content\Type\ContentUpdater\Action[]
     */
    public function determineActions(Type $fromType, Type $toType)
    {
        $actions = [];
        foreach ($fromType->fieldDefinitions as $fieldDef) {
            if (!$this->hasFieldDefinition($toType, $fieldDef)) {
                $actions[] = new ContentUpdater\Action\RemoveField(
                    $this->contentGateway,
                    $fieldDef,
                    $this->storageHandler,
                    $this->contentMapper
                );
            }
        }
        foreach ($toType->fieldDefinitions as $fieldDef) {
            if (!$this->hasFieldDefinition($fromType, $fieldDef)) {
                $actions[] = new ContentUpdater\Action\AddField(
                    $this->contentGateway,
                    $fieldDef,
                    $this->converterRegistry->getConverter(
                        $fieldDef->fieldType
                    ),
                    $this->storageHandler,
                    $this->contentMapper
                );
            }
        }

        return $actions;
    }

    /**
     * hasFieldDefinition.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\Type $type
     * @param \Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition $fieldDef
     *
     * @return bool
     */
    protected function hasFieldDefinition(Type $type, FieldDefinition $fieldDef)
    {
        foreach ($type->fieldDefinitions as $existFieldDef) {
            if ($existFieldDef->id == $fieldDef->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Applies all given updates.
     *
     * @param mixed $contentTypeId
     * @param \Ibexa\Core\Persistence\Legacy\Content\Type\ContentUpdater\Action[] $actions
     */
    public function applyUpdates($contentTypeId, array $actions)
    {
        if (empty($actions)) {
            return;
        }

        foreach ($this->getContentIdsByContentTypeId($contentTypeId) as $contentId) {
            foreach ($actions as $action) {
                $action->apply($contentId);
            }
        }
    }

    /**
     * Returns all content objects of $contentTypeId.
     *
     * @param mixed $contentTypeId
     *
     * @return int[]
     */
    protected function getContentIdsByContentTypeId($contentTypeId)
    {
        return $this->contentGateway->getContentIdsByContentTypeId($contentTypeId);
    }
}

class_alias(ContentUpdater::class, 'eZ\Publish\Core\Persistence\Legacy\Content\Type\ContentUpdater');
