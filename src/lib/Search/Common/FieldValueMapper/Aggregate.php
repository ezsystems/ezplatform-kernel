<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Common\FieldValueMapper;

use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Search\Field;
use Ibexa\Core\Search\Common\FieldValueMapper;

/**
 * Common aggregate mapper implementation.
 */
class Aggregate extends FieldValueMapper
{
    /**
     * Array of available mappers.
     *
     * @var \Ibexa\Core\Search\Common\FieldValueMapper[]
     */
    protected $mappers = [];

    /**
     * Construct from optional mapper array.
     *
     * @param \Ibexa\Core\Search\Common\FieldValueMapper[] $mappers
     */
    public function __construct(array $mappers = [])
    {
        foreach ($mappers as $mapper) {
            $this->addMapper($mapper);
        }
    }

    /**
     * Adds mapper.
     *
     * @param \Ibexa\Core\Search\Common\FieldValueMapper $mapper
     */
    public function addMapper(FieldValueMapper $mapper)
    {
        $this->mappers[] = $mapper;
    }

    /**
     * Check if field can be mapped.
     *
     * @param \Ibexa\Contracts\Core\Search\Field $field
     *
     * @return bool
     */
    public function canMap(Field $field)
    {
        return true;
    }

    /**
     * Map field value to a proper search engine representation.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException
     *
     * @param \Ibexa\Contracts\Core\Search\Field $field
     *
     * @return mixed
     */
    public function map(Field $field)
    {
        foreach ($this->mappers as $mapper) {
            if ($mapper->canMap($field)) {
                return $mapper->map($field);
            }
        }

        throw new NotImplementedException(
            'No mapper available for: ' . get_class($field->type)
        );
    }
}

class_alias(Aggregate::class, 'eZ\Publish\Core\Search\Common\FieldValueMapper\Aggregate');
