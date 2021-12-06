<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter;

use Ibexa\Contracts\Core\FieldType\ValueSerializerInterface;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Core\FieldType\FieldSettings;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter as ConverterInterface;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue;

final class SerializableConverter implements ConverterInterface
{
    /** @var \Ibexa\Contracts\Core\FieldType\ValueSerializerInterface */
    private $serializer;

    public function __construct(ValueSerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function toStorageValue(FieldValue $value, StorageFieldValue $storageFieldValue): void
    {
        $data = $value->data;
        if ($data !== null) {
            $data = $this->serializer->encode($data);
        }

        $storageFieldValue->dataText = $data;
        $storageFieldValue->sortKeyString = (string)$value->sortKey;
    }

    public function toFieldValue(StorageFieldValue $value, FieldValue $fieldValue): void
    {
        $data = $value->dataText;
        if ($data !== null) {
            $data = $this->serializer->decode($data);
        }

        $fieldValue->data = $data;
        $fieldValue->sortKey = $value->sortKeyString;
    }

    public function toStorageFieldDefinition(FieldDefinition $fieldDef, StorageFieldDefinition $storageDef): void
    {
        $settings = $fieldDef->fieldTypeConstraints->fieldSettings;
        if ($settings !== null) {
            $settings = $this->serializer->encode((array)$settings);
        }

        $storageDef->dataText5 = $settings;
    }

    public function toFieldDefinition(StorageFieldDefinition $storageDef, FieldDefinition $fieldDef): void
    {
        $settings = $storageDef->dataText5;
        if ($settings !== null) {
            $settings = new FieldSettings($this->serializer->decode($settings));
        }

        $fieldDef->fieldTypeConstraints->fieldSettings = $settings;
    }

    public function getIndexColumn(): string
    {
        return 'sort_key_string';
    }
}

class_alias(SerializableConverter::class, 'eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter\SerializableConverter');
