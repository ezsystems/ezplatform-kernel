<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\QueryType\BuiltIn\SortSpec\SortClauseParser;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\Exception\UnsupportedSortClauseException;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\SortClauseParserInterface;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\SortSpecParserInterface;

/**
 * Parser for sort clauses which expect only sort direction in constructor parameter.
 */
final class DefaultSortClauseParser implements SortClauseParserInterface
{
    /** @var string[] */
    private $valueObjectClassMap;

    public function __construct(array $valueObjectClassMap)
    {
        $this->valueObjectClassMap = $valueObjectClassMap;
    }

    public function parse(SortSpecParserInterface $parser, string $name): SortClause
    {
        if (isset($this->valueObjectClassMap[$name])) {
            $class = $this->valueObjectClassMap[$name];

            return new $class($parser->parseSortDirection());
        }

        throw new UnsupportedSortClauseException($name);
    }

    public function supports(string $name): bool
    {
        return isset($this->valueObjectClassMap[$name]);
    }
}

class_alias(DefaultSortClauseParser::class, 'eZ\Publish\Core\QueryType\BuiltIn\SortSpec\SortClauseParser\DefaultSortClauseParser');
