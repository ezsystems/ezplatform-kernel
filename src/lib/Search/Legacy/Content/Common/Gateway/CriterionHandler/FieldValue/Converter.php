<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\FieldValue;

use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use RuntimeException;

/**
 * Content locator gateway implementation using the DoctrineDatabase.
 */
class Converter
{
    /**
     * Criterion field value handler registry.
     *
     * @var \Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\FieldValue\HandlerRegistry
     */
    protected $registry;

    /**
     * Default Criterion field value handler.
     *
     * @var \Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\FieldValue\Handler
     */
    protected $defaultHandler;

    /**
     * Construct from an array of Criterion field value handlers.
     *
     * @param \Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\FieldValue\HandlerRegistry $registry
     * @param \Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\FieldValue\Handler|null $defaultHandler
     */
    public function __construct(HandlerRegistry $registry, Handler $defaultHandler = null)
    {
        $this->registry = $registry;
        $this->defaultHandler = $defaultHandler;
    }

    /**
     * Converts the criteria into query fragments.
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $outerQuery to be used only for parameter binding
     * @param \Doctrine\DBAL\Query\QueryBuilder $subQuery to modify Field Value query constraints
     *
     * @return \Doctrine\DBAL\Query\Expression\CompositeExpression|string
     *
     * @throws \RuntimeException if Criterion is not applicable to its target
     */
    public function convertCriteria(
        string $fieldTypeIdentifier,
        QueryBuilder $outerQuery,
        QueryBuilder $subQuery,
        Criterion $criterion,
        string $column
    ) {
        if ($this->registry->has($fieldTypeIdentifier)) {
            return $this->registry->get($fieldTypeIdentifier)->handle(
                $outerQuery,
                $subQuery,
                $criterion,
                $column
            );
        }

        if ($this->defaultHandler === null) {
            throw new RuntimeException(
                "No conversion for a Field Type '$fieldTypeIdentifier' found."
            );
        }

        return $this->defaultHandler->handle($outerQuery, $subQuery, $criterion, $column);
    }
}

class_alias(Converter::class, 'eZ\Publish\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\FieldValue\Converter');
