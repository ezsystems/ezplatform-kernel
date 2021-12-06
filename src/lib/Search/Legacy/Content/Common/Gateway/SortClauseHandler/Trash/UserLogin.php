<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Search\Legacy\Content\Common\Gateway\SortClauseHandler\Trash;

use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Core\Persistence\Legacy\User\Gateway as UserGateway;
use Ibexa\Core\Search\Legacy\Content\Common\Gateway\SortClauseHandler;

/**
 * @internal
 */
final class UserLogin extends SortClauseHandler
{
    public function accept(SortClause $sortClause): bool
    {
        return $sortClause instanceof SortClause\Trash\UserLogin;
    }

    public function applySelect(
        QueryBuilder $query,
        SortClause $sortClause,
        int $number
    ): array {
        $query
            ->addSelect(
                sprintf(
                    'u.login AS %s',
                    $column = $this->getSortColumnName($number)
                )
            );

        return (array)$column;
    }

    public function applyJoin(
        QueryBuilder $query,
        SortClause $sortClause,
        int $number,
        array $languageSettings
    ): void {
        $query->leftJoin(
            'c',
            UserGateway::USER_TABLE,
            'u',
            'c.owner_id = u.contentobject_id'
        );
    }
}

class_alias(UserLogin::class, 'eZ\Publish\Core\Search\Legacy\Content\Common\Gateway\SortClauseHandler\Trash\UserLogin');
