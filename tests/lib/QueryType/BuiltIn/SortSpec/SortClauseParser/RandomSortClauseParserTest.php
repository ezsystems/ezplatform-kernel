<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\QueryType\BuiltIn\SortSpec\SortClauseParser;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Random;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\SortClauseParser\RandomSortClauseParser;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\SortSpecParserInterface;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\Token;
use PHPUnit\Framework\TestCase;

final class RandomSortClauseParserTest extends TestCase
{
    private const EXAMPLE_SEED = 1;

    /** @var \Ibexa\Core\QueryType\BuiltIn\SortSpec\SortClauseParser\RandomSortClauseParser */
    private $randomSortClauseParser;

    protected function setUp(): void
    {
        $this->randomSortClauseParser = new RandomSortClauseParser();
    }

    public function testParse(): void
    {
        $parser = $this->createMock(SortSpecParserInterface::class);
        $parser
            ->method('isNextToken')
            ->with(Token::TYPE_INT)
            ->willReturn(true);

        $parser
            ->method('match')
            ->with(Token::TYPE_INT)
            ->willReturn(new Token(Token::TYPE_INT, (string)self::EXAMPLE_SEED));

        $parser->method('parseSortDirection')->willReturn(Query::SORT_ASC);

        $this->assertEquals(
            new Random(self::EXAMPLE_SEED, Query::SORT_ASC),
            $this->randomSortClauseParser->parse($parser, 'random')
        );
    }

    public function testSupports(): void
    {
        $this->assertTrue($this->randomSortClauseParser->supports('random'));
    }
}

class_alias(RandomSortClauseParserTest::class, 'eZ\Publish\Core\QueryType\BuiltIn\SortSpec\Tests\SortClauseParser\RandomSortClauseParserTest');
