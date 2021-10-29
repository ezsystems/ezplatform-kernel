<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class TermAggregationResultEntry extends ValueObject
{
    /** @var mixed */
    private $key;

    /** @var int */
    private $count;

    public function __construct($key, int $count)
    {
        parent::__construct();

        $this->key = $key;
        $this->count = $count;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}

class_alias(TermAggregationResultEntry::class, 'eZ\Publish\API\Repository\Values\Content\Search\AggregationResult\TermAggregationResultEntry');
