<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\QueryType\BuiltIn\SortSpec\SortClauseParser;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Field;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\SortClauseParser\FieldSortClauseParser;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\SortSpecParserInterface;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\Token;
use PHPUnit\Framework\TestCase;

final class FieldSortClauseParserTest extends TestCase
{
    private const EXAMPLE_CONTENT_TYPE_ID = 'article';
    private const EXAMPLE_FIELD_ID = 'title';

    /** @var \Ibexa\Core\QueryType\BuiltIn\SortSpec\SortClauseParser\FieldSortClauseParser */
    private $fieldSortClauseParser;

    protected function setUp(): void
    {
        $this->fieldSortClauseParser = new FieldSortClauseParser();
    }

    public function testParse(): void
    {
        $parser = $this->createMock(SortSpecParserInterface::class);
        $parser
            ->method('match')
            ->withConsecutive(
                [Token::TYPE_ID],
                [Token::TYPE_DOT],
                [Token::TYPE_ID]
            )
            ->willReturnOnConsecutiveCalls(
                new Token(Token::TYPE_ID, self::EXAMPLE_CONTENT_TYPE_ID),
                new Token(Token::TYPE_DOT),
                new Token(Token::TYPE_ID, self::EXAMPLE_FIELD_ID)
            );

        $parser->method('parseSortDirection')->willReturn(Query::SORT_ASC);

        $this->assertEquals(
            new Field(self::EXAMPLE_CONTENT_TYPE_ID, self::EXAMPLE_FIELD_ID, Query::SORT_ASC),
            $this->fieldSortClauseParser->parse($parser, 'field')
        );
    }

    public function testSupports(): void
    {
        $this->assertTrue($this->fieldSortClauseParser->supports('field'));
    }
}

class_alias(FieldSortClauseParserTest::class, 'eZ\Publish\Core\QueryType\BuiltIn\SortSpec\Tests\SortClauseParser\FieldSortClauseParserTest');
