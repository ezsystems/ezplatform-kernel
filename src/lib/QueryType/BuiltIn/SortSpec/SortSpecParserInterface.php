<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\QueryType\BuiltIn\SortSpec;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;

interface SortSpecParserInterface
{
    public function parseSortClausesList(): array;

    public function parseSortClause(): SortClause;

    public function parseSortDirection(): string;

    public function isNextToken(string ...$types): bool;

    public function match(string $type): Token;

    public function matchAnyOf(string ...$types): Token;
}

class_alias(SortSpecParserInterface::class, 'eZ\Publish\Core\QueryType\BuiltIn\SortSpec\SortSpecParserInterface');
