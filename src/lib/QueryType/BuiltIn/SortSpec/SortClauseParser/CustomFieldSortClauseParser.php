<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\QueryType\BuiltIn\SortSpec\SortClauseParser;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\CustomField;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\SortClauseParserInterface;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\SortSpecParserInterface;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\Token;

/**
 * Parser for {@see \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\CustomField}
 * sort clause.
 *
 * Example of correct input:
 *
 *      custom_field custom_field_name ASC
 */
final class CustomFieldSortClauseParser implements SortClauseParserInterface
{
    private const SUPPORTED_CLAUSE_NAME = 'custom_field';

    public function parse(SortSpecParserInterface $parser, string $name): SortClause
    {
        $args = [];
        $args[] = $parser->match(Token::TYPE_ID)->getValue();
        $args[] = $parser->parseSortDirection();

        return new CustomField(...$args);
    }

    public function supports(string $name): bool
    {
        return $name === self::SUPPORTED_CLAUSE_NAME;
    }
}

class_alias(CustomFieldSortClauseParser::class, 'eZ\Publish\Core\QueryType\BuiltIn\SortSpec\SortClauseParser\CustomFieldSortClauseParser');
