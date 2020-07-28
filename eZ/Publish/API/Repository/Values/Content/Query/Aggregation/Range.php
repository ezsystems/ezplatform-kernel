<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Values\Content\Query\Aggregation;

use eZ\Publish\API\Repository\Values\ValueObject;

final class Range extends ValueObject
{
    /**
     * Beginning of the range (included).
     *
     * @var int|float|\DateTimeInterface|null
     */
    private $from;

    /**
     * End of the range (excluded).
     *
     * @var int|float|\DateTimeInterface|null
     */
    private $to;

    public function __construct($form, $to)
    {
        parent::__construct();

        $this->from = $form;
        $this->to = $to;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function getTo()
    {
        return $this->to;
    }

    public function __toString(): string
    {
        return implode(' - ', [$this->from, $this->to]);
    }
}
