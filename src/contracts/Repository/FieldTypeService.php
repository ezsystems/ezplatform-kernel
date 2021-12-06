<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository;

/**
 * An implementation of this class provides access to FieldTypes.
 *
 * @see \Ibexa\Contracts\Core\Repository\FieldType
 */
interface FieldTypeService
{
    /**
     * Returns a list of all field types.
     *
     * @return \Ibexa\Contracts\Core\Repository\FieldType[]
     */
    public function getFieldTypes(): iterable;

    /**
     * Returns the FieldType registered with the given identifier.
     *
     * @param string $identifier
     *
     * @return \Ibexa\Contracts\Core\Repository\FieldType
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException if there is no FieldType registered with $identifier
     */
    public function getFieldType(string $identifier): FieldType;

    /**
     * Returns if there is a FieldType registered under $identifier.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasFieldType(string $identifier): bool;
}

class_alias(FieldTypeService::class, 'eZ\Publish\API\Repository\FieldTypeService');
