<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\QueryType\BuiltIn;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Subtree;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Visibility;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Core\MVC\ConfigResolverInterface;
use Ibexa\Core\QueryType\OptionsResolverBasedQueryType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractQueryType extends OptionsResolverBasedQueryType
{
    public const DEFAULT_LIMIT = 25;

    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    protected $repository;

    /** @var \Ibexa\Core\MVC\ConfigResolverInterface */
    protected $configResolver;

    /** @var \Ibexa\Core\QueryType\BuiltIn\SortClausesFactoryInterface */
    private $sortClausesFactory;

    public function __construct(
        Repository $repository,
        ConfigResolverInterface $configResolver,
        SortClausesFactoryInterface $sortSpecParserFactory
    ) {
        $this->repository = $repository;
        $this->configResolver = $configResolver;
        $this->sortClausesFactory = $sortSpecParserFactory;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'filter' => static function (OptionsResolver $resolver): void {
                $resolver->setDefaults([
                    'content_type' => [],
                    'visible_only' => true,
                    'siteaccess_aware' => true,
                ]);

                $resolver->setAllowedTypes('content_type', 'array');
                $resolver->setAllowedTypes('visible_only', 'bool');
                $resolver->setAllowedTypes('siteaccess_aware', 'bool');
            },
            'offset' => 0,
            'limit' => self::DEFAULT_LIMIT,
            'sort' => [],
        ]);

        $resolver->setNormalizer('sort', function (Options $options, $value) {
            if (is_string($value)) {
                $value = $this->sortClausesFactory->createFromSpecification($value);
            }

            if (!is_array($value)) {
                $value = [$value];
            }

            return $value;
        });

        $resolver->setAllowedTypes('sort', ['string', 'array', SortClause::class]);
        $resolver->setAllowedTypes('offset', 'int');
        $resolver->setAllowedTypes('limit', 'int');
    }

    abstract protected function getQueryFilter(array $parameters): Criterion;

    protected function createQuery(): Query
    {
        return new Query();
    }

    protected function doGetQuery(array $parameters): Query
    {
        $query = $this->createQuery();
        $query->filter = $this->buildFilters($parameters);

        if ($parameters['sort'] !== null) {
            $query->sortClauses = $parameters['sort'];
        }

        $query->limit = $parameters['limit'];
        $query->offset = $parameters['offset'];

        return $query;
    }

    private function buildFilters(array $parameters): Criterion
    {
        $criteria = [
            $this->getQueryFilter($parameters),
        ];

        if ($parameters['filter']['visible_only']) {
            $criteria[] = new Visibility(Visibility::VISIBLE);
        }

        if (!empty($parameters['filter']['content_type'])) {
            $criteria[] = new ContentTypeIdentifier($parameters['filter']['content_type']);
        }

        if ($parameters['filter']['siteaccess_aware']) {
            // Limit results to current SiteAccess tree root
            $criteria[] = new Subtree($this->getRootLocationPathString());
        }

        return new LogicalAnd($criteria);
    }

    private function getRootLocationPathString(): string
    {
        $rootLocation = $this->repository->getLocationService()->loadLocation(
            $this->configResolver->getParameter('content.tree_root.location_id')
        );

        return $rootLocation->pathString;
    }
}

class_alias(AbstractQueryType::class, 'eZ\Publish\Core\QueryType\BuiltIn\AbstractQueryType');
