<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Persistence\Legacy\Content\Type\ContentUpdater\Action;

use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\Gateway as ContentGateway;
use Ibexa\Core\Persistence\Legacy\Content\Mapper as ContentMapper;
use Ibexa\Core\Persistence\Legacy\Content\StorageHandler;
use Ibexa\Core\Persistence\Legacy\Content\Type\ContentUpdater\Action;

/**
 * Action to remove a field from content objects.
 */
class RemoveField extends Action
{
    /**
     * Field definition of the field to remove.
     *
     * @var \Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition
     */
    protected $fieldDefinition;

    /**
     * Storage handler.
     *
     * @var \Ibexa\Core\Persistence\Legacy\Content\StorageHandler
     */
    protected $storageHandler;

    /** @var \Ibexa\Core\Persistence\Legacy\Content\Mapper */
    protected $contentMapper;

    /**
     * Creates a new action.
     *
     * @param \Ibexa\Core\Persistence\Legacy\Content\Gateway $contentGateway
     * @param \Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition $fieldDef
     * @param \Ibexa\Core\Persistence\Legacy\Content\StorageHandler $storageHandler
     * @param \Ibexa\Core\Persistence\Legacy\Content\Mapper $contentMapper
     */
    public function __construct(
        ContentGateway $contentGateway,
        FieldDefinition $fieldDef,
        StorageHandler $storageHandler,
        ContentMapper $contentMapper
    ) {
        $this->contentGateway = $contentGateway;
        $this->fieldDefinition = $fieldDef;
        $this->storageHandler = $storageHandler;
        $this->contentMapper = $contentMapper;
    }

    /**
     * Applies the action to the given $content.
     *
     * @param int $contentId
     */
    public function apply($contentId)
    {
        $versionNumbers = $this->contentGateway->listVersionNumbers($contentId);
        $fieldIdSet = [];

        $nameRows = $this->contentGateway->loadVersionedNameData(
            array_map(
                static function ($versionNo) use ($contentId) {
                    return ['id' => $contentId, 'version' => $versionNo];
                },
                $versionNumbers
            )
        );

        foreach ($versionNumbers as $versionNo) {
            $contentRows = $this->contentGateway->load($contentId, $versionNo);
            $contentList = $this->contentMapper->extractContentFromRows($contentRows, $nameRows);
            $content = $contentList[0];
            $versionFieldIdSet = [];

            foreach ($content->fields as $field) {
                if ($field->fieldDefinitionId == $this->fieldDefinition->id) {
                    $fieldIdSet[$field->id] = true;
                    $versionFieldIdSet[$field->id] = true;
                }
            }

            // Delete from external storage with list of IDs per version
            $this->storageHandler->deleteFieldData(
                $this->fieldDefinition->fieldType,
                $content->versionInfo,
                array_keys($versionFieldIdSet)
            );
        }

        // Delete from relations storage
        $this->contentGateway->removeRelationsByFieldDefinitionId($this->fieldDefinition->id);

        // Delete from internal storage -- field is always deleted from _all_ versions
        foreach (array_keys($fieldIdSet) as $fieldId) {
            $this->contentGateway->deleteField($fieldId);
        }
    }
}

class_alias(RemoveField::class, 'eZ\Publish\Core\Persistence\Legacy\Content\Type\ContentUpdater\Action\RemoveField');
