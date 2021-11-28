<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Search\Legacy\Content\Common\Gateway\SortClauseHandler\Trash;

use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Core\Persistence\Legacy\Content\Type\Gateway as ContentTypeGateway;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\SortClauseHandler;

/**
 * @internal
 */
final class ContentTypeName extends SortClauseHandler
{
    public function accept(SortClause $sortClause): bool
    {
        return $sortClause instanceof SortClause\Trash\ContentTypeName;
    }

    public function applySelect(
        QueryBuilder $query,
        SortClause $sortClause,
        int $number
    ): array {
        $query
            ->addSelect(
                sprintf(
                    'ctn.name AS %s',
                    $column = $this->getSortColumnName($number)
                )
            );

        return [$column];
    }

    public function applyJoin(
        QueryBuilder $query,
        SortClause $sortClause,
        int $number,
        array $languageSettings
    ): void {
        $query->innerJoin(
            'c',
            ContentTypeGateway::CONTENT_TYPE_TABLE,
            'ct',
            'c.contentclass_id = ct.id'
        )->innerJoin(
            'ct',
            ContentTypeGateway::CONTENT_TYPE_NAME_TABLE,
            'ctn',
            'ctn.contentclass_id = ct.id'
        );
    }
}

class_alias(ContentTypeName::class, 'eZ\Publish\Core\Search\Legacy\Content\Common\Gateway\SortClauseHandler\Trash\ContentTypeName');
