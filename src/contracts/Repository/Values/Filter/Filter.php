<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Filter;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\Base\Exceptions\BadStateException;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use function sprintf;

/**
 * Content & Location filtering input Filter.
 */
final class Filter
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion|null */
    private $criterion;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Filter\FilteringSortClause[] */
    private $sortClauses = [];

    /** @var int */
    private $offset = 0;

    /** @var int */
    private $limit = 0;

    /**
     * Build Filter.
     *
     * It's recommended to skip arguments and use `with...` and `andWith...` methods to build Filter.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException for invalid Sort Clause
     */
    public function __construct(?FilteringCriterion $criterion = null, array $sortClauses = [])
    {
        $this->criterion = $criterion;
        foreach ($sortClauses as $idx => $sortClause) {
            if (!$sortClause instanceof FilteringSortClause) {
                throw new BadStateException(
                    '$sortClauses',
                    sprintf(
                        'Expected an instance of "%s", got "%s" at position %d',
                        FilteringSortClause::class,
                        is_object($sortClause) ? get_class($sortClause) : gettype($sortClause),
                        $idx
                    )
                );
            }

            $this->sortClauses[] = $sortClause;
        }
    }

    /**
     * Reset Filter so it can be built from scratch.
     */
    public function reset(): self
    {
        $this->criterion = null;
        $this->sortClauses = [];

        return $this;
    }

    /**
     * Set filtering Criterion.
     *
     * If multiple Criteria are required, either use `andWithCriterion`/`orWithCriterion` or wrap
     * them with Logical operator Criterion.
     *
     * To re-build Criterion from scratch `reset` it first.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException if Criterion is already set
     *
     * @see reset
     * @see andWithCriterion
     * @see orWithCriterion
     * @see \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalOr
     * @see \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd
     */
    public function withCriterion(FilteringCriterion $criterion): self
    {
        if (null !== $this->criterion) {
            throw new BadStateException(
                '$criterion',
                'Criterion is already set. ' .
                'To append Criterion invoke either "andWithCriterion" or "orWithCriterion". ' .
                'To start building Criterion from scratch "reset" it first.'
            );
        }

        $this->criterion = $criterion;

        return $this;
    }

    /**
     * @see withCriterion
     */
    public function andWithCriterion(FilteringCriterion $criterion): self
    {
        if (null === $this->criterion) {
            // for better DX allow operation on uninitialized Criterion by setting it as-is
            $this->criterion = $criterion;
        } elseif ($this->criterion instanceof Criterion\LogicalAnd) {
            $this->criterion->criteria[] = $criterion;
        } else {
            $this->criterion = new Criterion\LogicalAnd([$this->criterion, $criterion]);
        }

        return $this;
    }

    /**
     * @see withCriterion
     */
    public function orWithCriterion(FilteringCriterion $criterion): self
    {
        if (null === $this->criterion) {
            // for better DX allow operation on uninitialized Criterion by setting it as-is
            $this->criterion = $criterion;
        } elseif ($this->criterion instanceof Criterion\LogicalOr) {
            $this->criterion->criteria[] = $criterion;
        } else {
            $this->criterion = new Criterion\LogicalOr([$this->criterion, $criterion]);
        }

        return $this;
    }

    public function withSortClause(FilteringSortClause $sortClause): self
    {
        $this->sortClauses[] = $sortClause;

        return $this;
    }

    public function withOffset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    public function withLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Request result dataset slice by setting page limit and offset.
     * Both values MUST be `>=0`.
     *
     * @param int $limit >=0, use 0 for no limit.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function sliceBy(int $limit, int $offset): self
    {
        if ($limit < 0) {
            throw new InvalidArgumentException(
                '$limit',
                sprintf('Filtering slice limit needs to be >=0, got %d', $limit)
            );
        }

        if ($offset < 0) {
            throw new InvalidArgumentException(
                '$offset',
                sprintf('Filtering slice offset needs to be >=0, got %d', $offset)
            );
        }

        $this->limit = $limit;
        $this->offset = $offset;

        return $this;
    }

    public function getCriterion(): ?FilteringCriterion
    {
        return $this->criterion;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Filter\FilteringSortClause[]
     */
    public function getSortClauses(): array
    {
        return $this->sortClauses;
    }

    /**
     * Get offset set by sliceBy.
     *
     * @see sliceBy
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * Get limit set by sliceBy.
     *
     * @see sliceBy
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    public function __clone()
    {
        $this->criterion = $this->criterion !== null ? clone $this->criterion : null;
        $this->sortClauses = array_map(
            static function (FilteringSortClause $sortClause): FilteringSortClause {
                return clone $sortClause;
            },
            $this->sortClauses
        );
    }
}

class_alias(Filter::class, 'eZ\Publish\API\Repository\Values\Filter\Filter');
