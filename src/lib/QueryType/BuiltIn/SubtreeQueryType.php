<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\QueryType\BuiltIn;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Location\Depth;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\MatchNone;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Subtree;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SubtreeQueryType extends AbstractLocationQueryType
{
    public static function getName(): string
    {
        return 'Subtree';
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'depth' => -1,
        ]);
        $resolver->setAllowedTypes('depth', 'int');
    }

    protected function getQueryFilter(array $parameters): Criterion
    {
        $location = $this->resolveLocation($parameters);

        if ($location === null) {
            return new MatchNone();
        }

        if ($parameters['depth'] > -1) {
            $depth = $location->depth + (int)$parameters['depth'];

            return new LogicalAnd([
                new Subtree($location->pathString),
                new Depth(Operator::LTE, $depth),
            ]);
        }

        return new Subtree($location->pathString);
    }
}

class_alias(SubtreeQueryType::class, 'eZ\Publish\Core\QueryType\BuiltIn\SubtreeQueryType');
