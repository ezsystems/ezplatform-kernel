<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\QueryType\BuiltIn\SortSpec\SortClauseParser;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Field;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\SortClauseParserInterface;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\SortSpecParserInterface;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\Token;

/**
 * Parser for {@see \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Field} sort clause.
 *
 * Example of correct input:
 *
 *      field article.short_title DESC
 */
final class FieldSortClauseParser implements SortClauseParserInterface
{
    private const SUPPORTED_CLAUSE_NAME = 'field';

    public function parse(SortSpecParserInterface $parser, string $name): SortClause
    {
        $args = [];
        $args[] = $parser->match(Token::TYPE_ID)->getValue();
        $parser->match(Token::TYPE_DOT);
        $args[] = $parser->match(Token::TYPE_ID)->getValue();
        $args[] = $parser->parseSortDirection();

        return new Field(...$args);
    }

    public function supports(string $name): bool
    {
        return $name === self::SUPPORTED_CLAUSE_NAME;
    }
}

class_alias(FieldSortClauseParser::class, 'eZ\Publish\Core\QueryType\BuiltIn\SortSpec\SortClauseParser\FieldSortClauseParser');
