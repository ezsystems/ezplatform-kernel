<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\QueryType\BuiltIn\SortSpec;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\SortClauseParserInterface;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\SortSpecLexerInterface;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\SortSpecParser;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\Token;
use Ibexa\Tests\Core\Search\TestCase;

final class SortSpecParserTest extends TestCase
{
    private const EXAMPLE_SORT_CLAUSE_ID = 'id';

    /**
     * @dataProvider dataProviderForParseSortDirection
     */
    public function testParseSortDirection(array $input, string $expectedDirection): void
    {
        $lexer = new SortSpecLexerStub($input);
        $parser = new SortSpecParser($this->createMock(SortClauseParserInterface::class), $lexer);

        $this->assertEquals($expectedDirection, $parser->parseSortDirection());
    }

    public function dataProviderForParseSortDirection(): iterable
    {
        yield 'asc' => [
            [
                new Token(Token::TYPE_ASC),
                new Token(Token::TYPE_EOF),
            ],
            Query::SORT_ASC,
        ];

        yield 'desc' => [
            [
                new Token(Token::TYPE_DESC),
                new Token(Token::TYPE_EOF),
            ],
            Query::SORT_DESC,
        ];

        yield 'default' => [
            [
                new Token(Token::TYPE_EOF),
            ],
            Query::SORT_ASC,
        ];
    }

    public function testParseSortClauseList(): void
    {
        $lexer = new SortSpecLexerStub([
            new Token(Token::TYPE_ID, self::EXAMPLE_SORT_CLAUSE_ID),
            new Token(Token::TYPE_COMMA),
            new Token(Token::TYPE_ID, self::EXAMPLE_SORT_CLAUSE_ID),
            new Token(Token::TYPE_EOF),
        ]);

        $sortClauseArgsParser = $this->createMock(SortClauseParserInterface::class);
        $parser = new SortSpecParser($sortClauseArgsParser, $lexer);

        $sortClauseA = $this->createMock(SortClause::class);
        $sortClauseB = $this->createMock(SortClause::class);

        $sortClauseArgsParser
            ->method('parse')
            ->with($parser, self::EXAMPLE_SORT_CLAUSE_ID)
            ->willReturnOnConsecutiveCalls($sortClauseA, $sortClauseB);

        $this->assertEquals(
            [$sortClauseA, $sortClauseB],
            $parser->parseSortClausesList()
        );
    }

    public function testParseSortClause(): void
    {
        $lexer = new SortSpecLexerStub([
            new Token(Token::TYPE_ID, self::EXAMPLE_SORT_CLAUSE_ID),
            new Token(Token::TYPE_EOF),
        ]);

        $sortClauseArgsParser = $this->createMock(SortClauseParserInterface::class);
        $parser = new SortSpecParser($sortClauseArgsParser, $lexer);

        $sortClause = $this->createMock(SortClause::class);
        $sortClauseArgsParser
            ->expects($this->once())
            ->method('parse')
            ->with($parser, self::EXAMPLE_SORT_CLAUSE_ID)
            ->willReturn($sortClause);

        $this->assertEquals($sortClause, $parser->parseSortClause());
    }

    public function testMatch(): void
    {
        $token = new Token(Token::TYPE_ID, self::EXAMPLE_SORT_CLAUSE_ID);

        $lexer = $this->createMock(SortSpecLexerInterface::class);
        $lexer->expects($this->once())->method('peek')->willReturn($token);
        $lexer->expects($this->once())->method('consume')->willReturn($token);

        $parser = new SortSpecParser(
            $this->createMock(SortClauseParserInterface::class),
            $lexer
        );

        $this->assertEquals($token, $parser->match(Token::TYPE_ID));
    }

    public function testMatchAny(): void
    {
        $token = new Token(Token::TYPE_ASC);

        $lexer = $this->createMock(SortSpecLexerInterface::class);
        $lexer->expects($this->once())->method('peek')->willReturn($token);
        $lexer->expects($this->once())->method('consume')->willReturn($token);

        $parser = new SortSpecParser(
            $this->createMock(SortClauseParserInterface::class),
            $lexer
        );

        $this->assertEquals($token, $parser->matchAnyOf(Token::TYPE_ASC, Token::TYPE_DESC));
    }
}

class_alias(SortSpecParserTest::class, 'eZ\Publish\Core\QueryType\BuiltIn\SortSpec\Tests\SortSpecParserTest');
