<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\QueryType\BuiltIn;

/**
 * @internal
 */
interface SortClausesFactoryInterface
{
    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[]
     *
     * @throws \Ibexa\Core\QueryType\BuiltIn\SortSpec\Exception\SyntaxErrorException
     */
    public function createFromSpecification(string $specification): array;
}

class_alias(SortClausesFactoryInterface::class, 'eZ\Publish\Core\QueryType\BuiltIn\SortClausesFactoryInterface');
