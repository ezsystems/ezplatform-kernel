<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Repository;

use eZ\Publish\API\Repository\FieldType as APIFieldType;
use eZ\Publish\API\Repository\FieldTypeService as FieldTypeServiceInterface;
use eZ\Publish\Core\FieldType\FieldTypeRegistry;
use eZ\Publish\Core\Repository\Values\ContentType\FieldType;

/**
 * An implementation of this class provides access to FieldTypes.
 *
 * @see \eZ\Publish\API\Repository\FieldType
 */
class FieldTypeService implements FieldTypeServiceInterface
{
    /** @var \eZ\Publish\Core\FieldType\FieldTypeRegistry */
    protected $fieldTypeRegistry;

    /**
     * Holds an array of FieldType objects to avoid re creating them all the time from SPI variants.
     *
     * @var \eZ\Publish\API\Repository\FieldType[]
     */
    protected $fieldTypes = [];

    /**
     * Setups service with reference to repository object that created it & corresponding handler.
     *
     * @param \eZ\Publish\Core\FieldType\FieldTypeRegistry $fieldTypeRegistry Registry for SPI FieldTypes
     */
    public function __construct(FieldTypeRegistry $fieldTypeRegistry)
    {
        $this->fieldTypeRegistry = $fieldTypeRegistry;
    }

    /**
     * Returns a list of all field types.
     *
     * @return \eZ\Publish\API\Repository\FieldType[]
     */
    public function getFieldTypes(): iterable
    {
        foreach ($this->fieldTypeRegistry->getFieldTypes() as $identifier => $spiFieldType) {
            if (isset($this->fieldTypes[$identifier])) {
                continue;
            }

            $this->fieldTypes[$identifier] = new FieldType($spiFieldType);
        }

        return $this->fieldTypes;
    }

    /**
     * Returns the FieldType registered with the given identifier.
     *
     * @param string $identifier
     *
     * @return \eZ\Publish\API\Repository\FieldType
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException if there is no FieldType registered with $identifier
     */
    public function getFieldType(string $identifier): APIFieldType
    {
        if (isset($this->fieldTypes[$identifier])) {
            return $this->fieldTypes[$identifier];
        }

        return $this->fieldTypes[$identifier] = new FieldType($this->fieldTypeRegistry->getFieldType($identifier));
    }

    /**
     * Returns if there is a FieldType registered under $identifier.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasFieldType(string $identifier): bool
    {
        return $this->fieldTypeRegistry->hasFieldType($identifier);
    }
}
