<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Persistence;

use Ibexa\Contracts\Core\FieldType\FieldType as SPIFieldType;
use Ibexa\Contracts\Core\Persistence\FieldType as FieldTypeInterface;
use Ibexa\Core\Base\Exceptions\NotFound\FieldTypeNotFoundException;

/**
 * Registry for field types available to storage engines.
 */
class FieldTypeRegistry
{
    /**
     * Map of FieldTypes where key is field type identifier and value is FieldType object complying
     * to {@link \Ibexa\Contracts\Core\FieldType\FieldType} interface.
     *
     * @var \Ibexa\Contracts\Core\FieldType\FieldType[]
     */
    protected $coreFieldTypes;

    /**
     * Map of FieldTypes where key is field type identifier and value is FieldType object.
     *
     * @var \Ibexa\Contracts\Core\Persistence\FieldType[]
     */
    protected $fieldTypes;

    /**
     * Creates FieldType registry.
     *
     * In $fieldTypes a mapping of field type identifier to object is expected.
     * The FieldType object must comply to the {@link \Ibexa\Contracts\Core\FieldType\FieldType} interface.
     *
     * @param \Ibexa\Core\Persistence\FieldType[] $coreFieldTypes
     * @param \Ibexa\Contracts\Core\FieldType\FieldType[] $fieldTypes A map where key is field type identifier and value is
     *                                                          a callable factory to get FieldType OR FieldType object.
     */
    public function __construct(array $coreFieldTypes = [], array $fieldTypes = [])
    {
        $this->coreFieldTypes = $coreFieldTypes;
        $this->fieldTypes = $fieldTypes;
    }

    /**
     * Returns the FieldType object for given $identifier.
     *
     * @param string $identifier
     *
     * @return \Ibexa\Contracts\Core\Persistence\FieldType
     *
     * @throws \RuntimeException If field type for given $identifier is not instance or callable.
     * @throws \Ibexa\Core\Base\Exceptions\NotFound\FieldTypeNotFoundException If field type for given $identifier is not found.
     */
    public function getFieldType(string $identifier): FieldTypeInterface
    {
        if (!isset($this->fieldTypes[$identifier])) {
            $this->fieldTypes[$identifier] = new FieldType($this->getCoreFieldType($identifier));
        }

        return $this->fieldTypes[$identifier];
    }

    public function register(string $identifier, SPIFieldType $fieldType): void
    {
        $this->coreFieldTypes[$identifier] = $fieldType;
    }

    protected function getCoreFieldType(string $identifier): SPIFieldType
    {
        if (!isset($this->coreFieldTypes[$identifier])) {
            throw new FieldTypeNotFoundException($identifier);
        }

        return $this->coreFieldTypes[$identifier];
    }
}

class_alias(FieldTypeRegistry::class, 'eZ\Publish\Core\Persistence\FieldTypeRegistry');
