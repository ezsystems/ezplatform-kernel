<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\QueryType\BuiltIn\SortSpec\SortClauseParser;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Random;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\SortClauseParserInterface;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\SortSpecParserInterface;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\Token;

/**
 * Parser for {@see \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Random} sort
 * clause.
 *
 * Example of correct input:
 *
 *      random 7 ASC
 */
final class RandomSortClauseParser implements SortClauseParserInterface
{
    private const SUPPORTED_CLAUSE_NAME = 'random';

    public function parse(SortSpecParserInterface $parser, string $name): SortClause
    {
        $seed = null;
        if ($parser->isNextToken(Token::TYPE_INT)) {
            $seed = $parser->match(Token::TYPE_INT)->getValueAsInt();
        }

        $sortDirection = $parser->parseSortDirection();

        return new Random($seed, $sortDirection);
    }

    public function supports(string $name): bool
    {
        return $name === self::SUPPORTED_CLAUSE_NAME;
    }
}

class_alias(RandomSortClauseParser::class, 'eZ\Publish\Core\QueryType\BuiltIn\SortSpec\SortClauseParser\RandomSortClauseParser');
