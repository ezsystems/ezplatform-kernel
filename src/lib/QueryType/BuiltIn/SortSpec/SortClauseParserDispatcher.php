<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\QueryType\BuiltIn\SortSpec;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Core\QueryType\BuiltIn\SortSpec\Exception\UnsupportedSortClauseException;

final class SortClauseParserDispatcher implements SortClauseParserInterface
{
    /** @var \Ibexa\Core\QueryType\BuiltIn\SortSpec\SortClauseParserInterface[] */
    private $parsers;

    public function __construct(iterable $parsers = [])
    {
        $this->parsers = $parsers;
    }

    public function parse(SortSpecParserInterface $parser, string $name): SortClause
    {
        $sortClauseParser = $this->findParser($name);
        if ($sortClauseParser instanceof SortClauseParserInterface) {
            return $sortClauseParser->parse($parser, $name);
        }

        throw new UnsupportedSortClauseException($name);
    }

    public function supports(string $name): bool
    {
        return $this->findParser($name) instanceof SortClauseParserInterface;
    }

    private function findParser(string $name): ?SortClauseParserInterface
    {
        foreach ($this->parsers as $parser) {
            if ($parser->supports($name)) {
                return $parser;
            }
        }

        return null;
    }
}

class_alias(SortClauseParserDispatcher::class, 'eZ\Publish\Core\QueryType\BuiltIn\SortSpec\SortClauseParserDispatcher');
