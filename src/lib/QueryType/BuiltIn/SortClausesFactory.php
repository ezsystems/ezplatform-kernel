<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\QueryType\BuiltIn;

use Ibexa\Core\QueryType\BuiltIn\SortSpec\SortClauseParserInterface;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\SortSpecLexer;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\SortSpecParser;

/**
 * @internal
 */
final class SortClausesFactory implements SortClausesFactoryInterface
{
    /** @var \Ibexa\Core\QueryType\BuiltIn\SortSpec\SortClauseParserInterface */
    private $sortClauseParser;

    public function __construct(SortClauseParserInterface $sortClauseArgsParser)
    {
        $this->sortClauseParser = $sortClauseArgsParser;
    }

    /**
     * @throws \Ibexa\Core\QueryType\BuiltIn\SortSpec\Exception\SyntaxErrorException
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[]
     */
    public function createFromSpecification(string $specification): array
    {
        $lexer = new SortSpecLexer();
        $lexer->tokenize($specification);

        $parser = new SortSpecParser($this->sortClauseParser, $lexer);

        return $parser->parseSortClausesList();
    }
}

class_alias(SortClausesFactory::class, 'eZ\Publish\Core\QueryType\BuiltIn\SortClausesFactory');
