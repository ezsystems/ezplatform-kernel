<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\QueryType;

/**
 * Registry of QueryType objects.
 */
interface QueryTypeRegistry
{
    /**
     * Registers $queryType as $name.
     *
     * @param string $name
     * @param \Ibexa\Core\QueryType\QueryType $queryType
     */
    public function addQueryType($name, QueryType $queryType);

    /**
     * Registers QueryTypes from the $queryTypes array.
     *
     * @param \Ibexa\Core\QueryType\QueryType[] $queryTypes An array of QueryTypes, with their name as the index
     */
    public function addQueryTypes(array $queryTypes);

    /**
     * Get the QueryType $name.
     *
     * @param string $name
     *
     * @return \Ibexa\Core\QueryType\QueryType
     */
    public function getQueryType($name);
}

class_alias(QueryTypeRegistry::class, 'eZ\Publish\Core\QueryType\QueryTypeRegistry');
