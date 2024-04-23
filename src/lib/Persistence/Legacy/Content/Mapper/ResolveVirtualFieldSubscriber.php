<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Content\Mapper;

use eZ\Publish\Core\FieldType\NullStorage;
use eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter\Exception\NotFound;
use eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\ConverterRegistry;
use eZ\Publish\Core\Persistence\Legacy\Content\Gateway as ContentGateway;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageRegistry;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\FieldValue;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;
use Ibexa\Contracts\Core\Event\Mapper\ResolveMissingFieldEvent;
use Ibexa\Contracts\Core\FieldType\DefaultDataFieldStorage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ResolveVirtualFieldSubscriber implements EventSubscriberInterface
{
    /** @var \eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\ConverterRegistry */
    private $converterRegistry;

    /** @var \eZ\Publish\Core\Persistence\Legacy\Content\StorageRegistry */
    private $storageRegistry;

    /** @var \eZ\Publish\Core\Persistence\Legacy\Content\Gateway */
    private $contentGateway;

    public function __construct(
        ConverterRegistry $converterRegistry,
        StorageRegistry $storageRegistry,
        ContentGateway $contentGateway
    ) {
        $this->converterRegistry = $converterRegistry;
        $this->storageRegistry = $storageRegistry;
        $this->contentGateway = $contentGateway;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ResolveMissingFieldEvent::class => [
                ['persistExternalStorageField', -100],
                ['resolveVirtualExternalStorageField', -80],
                ['resolveVirtualField', 0],
            ],
        ];
    }

    public function resolveVirtualField(ResolveMissingFieldEvent $event): void
    {
        if ($event->getField()) {
            return;
        }

        $content = $event->getContent();

        try {
            $emptyField = $this->createEmptyField(
                $content->versionInfo,
                $event->getFieldDefinition(),
                $event->getLanguageCode()
            );

            $event->setField($emptyField);
        } catch (NotFound $exception) {
            return;
        }
    }

    /**
     * @throws \eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter\Exception\NotFound
     */
    public function persistExternalStorageField(ResolveMissingFieldEvent $event): void
    {
        $field = $event->getField();

        if ($field !== null && $field->id !== null) {
            // Not a virtual field
            return;
        }

        $fieldDefinition = $event->getFieldDefinition();
        $storage = $this->storageRegistry->getStorage($fieldDefinition->fieldType);

        if ($storage instanceof NullStorage) {
            // Not an external storage
            return;
        }

        $content = $event->getContent();

        $field->id = $this->contentGateway->insertNewField(
            $content,
            $field,
            $this->getDefaultStorageValue()
        );

        if ($field->value->data !== null) {
            $result = $storage->storeFieldData(
                $content->versionInfo,
                $field,
                []
            );

            if ($result === true) {
                $storageValue = new StorageFieldValue();
                $converter = $this->converterRegistry->getConverter($fieldDefinition->fieldType);
                $converter->toStorageValue(
                    $field->value,
                    $storageValue
                );

                $this->contentGateway->updateField(
                    $field,
                    $storageValue
                );
            }
        }

        $storage->getFieldData(
            $content->versionInfo,
            $field,
            []
        );

        $event->setField($field);
    }

    public function resolveVirtualExternalStorageField(ResolveMissingFieldEvent $event): void
    {
        $field = $event->getField();

        if ($field !== null && $field->id !== null) {
            // Not a virtual field
            return;
        }

        $fieldDefinition = $event->getFieldDefinition();
        $storage = $this->storageRegistry->getStorage($fieldDefinition->fieldType);

        if ($storage instanceof NullStorage) {
            // Not an external storage
            return;
        }

        if (!$storage instanceof DefaultDataFieldStorage) {
            return;
        }

        $content = $event->getContent();

        $storage->getDefaultFieldData(
            $content->versionInfo,
            $field
        );

        $event->setField($field);

        // Do not persist the external storage field
        $event->stopPropagation();
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function createEmptyField(
        VersionInfo $versionInfo,
        FieldDefinition $fieldDefinition,
        string $languageCode
    ): Field {
        $field = new Field();
        $field->fieldDefinitionId = $fieldDefinition->id;
        $field->type = $fieldDefinition->fieldType;
        $field->value = $this->getDefaultValue($fieldDefinition);
        $field->languageCode = $languageCode;
        $field->versionNo = $versionInfo->versionNo;

        return $field;
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function getDefaultValue(FieldDefinition $fieldDefinition): FieldValue
    {
        $value = clone $fieldDefinition->defaultValue;
        $storageValue = $this->getDefaultStorageValue();

        $converter = $this->converterRegistry->getConverter($fieldDefinition->fieldType);
        $converter->toStorageValue($value, $storageValue);
        $converter->toFieldValue($storageValue, $value);

        return $value;
    }

    private function getDefaultStorageValue(): StorageFieldValue
    {
        $storageValue = new StorageFieldValue();
        $storageValue->dataFloat = null;
        $storageValue->dataInt = null;
        $storageValue->dataText = '';
        $storageValue->sortKeyInt = 0;
        $storageValue->sortKeyString = '';

        return $storageValue;
    }
}
