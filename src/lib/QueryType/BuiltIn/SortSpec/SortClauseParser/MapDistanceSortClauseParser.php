<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\QueryType\BuiltIn\SortSpec\SortClauseParser;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\MapLocationDistance;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\SortClauseParserInterface;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\SortSpecParserInterface;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\Token;

/**
 * Parser for {@see \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\MapLocationDistance}
 * sort clause.
 *
 * Example of correct input:
 *
 *      map_distance place.location 45.0809 14.5926 ASC
 */
final class MapDistanceSortClauseParser implements SortClauseParserInterface
{
    private const SUPPORTED_CLAUSE_NAME = 'map_distance';

    public function parse(SortSpecParserInterface $parser, string $name): SortClause
    {
        $args = [];
        $args[] = $parser->match(Token::TYPE_ID)->getValue();
        $parser->match(Token::TYPE_DOT);
        $args[] = $parser->match(Token::TYPE_ID)->getValue();
        $args[] = $parser->match(Token::TYPE_FLOAT)->getValueAsFloat();
        $args[] = $parser->match(Token::TYPE_FLOAT)->getValueAsFloat();
        $args[] = $parser->parseSortDirection();

        return new MapLocationDistance(...$args);
    }

    public function supports(string $name): bool
    {
        return $name === self::SUPPORTED_CLAUSE_NAME;
    }
}

class_alias(MapDistanceSortClauseParser::class, 'eZ\Publish\Core\QueryType\BuiltIn\SortSpec\SortClauseParser\MapDistanceSortClauseParser');
