<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;

/**
 * This class is used by Criteria to describe which operators they support.
 *
 * Instances of this class are returned in an array by the {@see Criterion::getSpecifications()} method
 */
class Specifications
{
    /**
     * Criterion input type description constants.
     */
    public const FORMAT_SINGLE = 1;
    public const FORMAT_ARRAY = 2;

    /**
     * Criterion input value type description constants.
     * Used by {@see getDescription()} to say which type of values an operator expects.
     */
    public const TYPE_INTEGER = 1;
    public const TYPE_STRING = 2;
    public const TYPE_BOOLEAN = 4;

    /**
     * Specified operator, as one of the Operator::* constants.
     */
    public $operator;

    /**
     * Format supported for the Criterion value, either {@see self::FORMAT_SINGLE} for single
     * or {@see self::FORMAT_ARRAY} for multiple.
     *
     * @see self::FORMAT_*
     *
     * @var int
     */
    public $valueFormat;

    /**
     * Accepted values types, specifying what type of variables are accepted as a value.
     *
     * @see self::TYPE_*
     *
     * @var int
     */
    public $valueTypes;

    /**
     * Limitation on the number of items as the value.
     *
     * Only usable if {@see $valueFormat} is {@see self::FORMAT_ARRAY}.
     * Not setting it means that 1...n will be required
     *
     * @var int
     */
    public $valueCount;

    /**
     * Creates a new Specifications object.
     *
     * @param string $operator The specified operator, as one of the Operator::* constants
     * @param int $valueFormat The accepted value format, either {@see self::FORMAT_ARRAY} or {@see self::FORMAT_SINGLE}
     * @param int $valueTypes The supported value types, as a bit field of the {@see self::TYPE_*} constants
     * @param int $valueCount The required number of values, when the accepted format is {@see self::FORMAT_ARRAY}
     */
    public function __construct(string $operator, int $valueFormat, ?int $valueTypes = null, ?int $valueCount = null)
    {
        $this->operator = $operator;
        $this->valueFormat = $valueFormat;
        $this->valueTypes = $valueTypes;
        $this->valueCount = $valueCount;
    }
}

class_alias(Specifications::class, 'eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator\Specifications');
