<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\QueryType\BuiltIn\SortSpec;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;

interface SortClauseParserInterface
{
    /**
     * @throws \Ibexa\Core\QueryType\BuiltIn\SortSpec\Exception\UnsupportedSortClauseException If sort clause is not supported by parser
     */
    public function parse(SortSpecParserInterface $parser, string $name): SortClause;

    public function supports(string $name): bool;
}

class_alias(SortClauseParserInterface::class, 'eZ\Publish\Core\QueryType\BuiltIn\SortSpec\SortClauseParserInterface');
