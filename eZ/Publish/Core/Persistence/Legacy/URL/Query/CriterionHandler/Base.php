<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Persistence\Legacy\URL\Query\CriterionHandler;

use Doctrine\DBAL\Query\QueryBuilder;
use eZ\Publish\Core\Persistence\Legacy\Content\Gateway as ContentGateway;
use eZ\Publish\Core\Persistence\Legacy\URL\Gateway\DoctrineDatabase;
use eZ\Publish\Core\Persistence\Legacy\URL\Query\CriterionHandler;

abstract class Base implements CriterionHandler
{
    /**
     * Inner join `ezurl_object_link` table if not joined yet.
     */
    protected function joinContentObjectLink(QueryBuilder $query): void
    {
        if (false === $this->hasJoinedTable($query, DoctrineDatabase::URL_LINK_TABLE)) {
            $query->innerJoin(
                'url',
                DoctrineDatabase::URL_LINK_TABLE,
                'u_lnk',
                'url.id = u_lnk.url_id'
            );
        }
    }

    /**
     * Inner join `ezcontentobject` table if not joined yet.
     */
    protected function joinContentObject(QueryBuilder $query): void
    {
        if (false === $this->hasJoinedTable($query, ContentGateway::CONTENT_ITEM_TABLE)) {
            $query->innerJoin(
                'f_def',
                ContentGateway::CONTENT_ITEM_TABLE,
                'c',
                'c.id = f_def.contentobject_id'
            );
        }
    }

    /**
     * Inner join `ezcontentobject_attribute` table if not joined yet.
     */
    protected function joinContentObjectAttribute(QueryBuilder $query): void
    {
        if (false === $this->hasJoinedTable($query, ContentGateway::CONTENT_FIELD_TABLE)) {
            $query->innerJoin(
                'u_lnk',
                ContentGateway::CONTENT_FIELD_TABLE,
                'f_def',
                $query->expr()->andX(
                    'u_lnk.contentobject_attribute_id = f_def.id',
                    'u_lnk.contentobject_attribute_version = f_def.version'
                )
            );
        }
    }

    protected function hasJoinedTable(QueryBuilder $queryBuilder, string $tableName): bool
    {
        $joinedParts = $queryBuilder->getQueryPart('join');
        if (empty($joinedParts)) {
            return false;
        }

        // extract 'joinTable' nested key and flatten the structure of query parts, which is:
        // ['fromAlias' => [['joinTable' => '<table_name>'], ...]]
        // note that one 'fromAlias' can have multiple different tables joined for it, though it's not usual case
        $joinedTables = array_merge(
            ...array_values(
                array_map(
                    static function (array $joinedPart): array {
                        return array_column($joinedPart, 'joinTable');
                    },
                    $joinedParts
                )
            )
        );

        return in_array($tableName, $joinedTables, true);
    }
}
