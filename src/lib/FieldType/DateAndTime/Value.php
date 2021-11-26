<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\DateAndTime;

use DateTime;
use Exception;
use Ibexa\Core\Base\Exceptions\InvalidArgumentValue;
use Ibexa\Core\FieldType\Value as BaseValue;

/**
 * Value for DateAndTime field type.
 */
class Value extends BaseValue
{
    /**
     * Date content.
     *
     * @var \DateTime|null
     */
    public $value;

    /**
     * Date format to be used by {@link __toString()}.
     *
     * @var string
     */
    public $stringFormat = 'U';

    /**
     * Construct a new Value object and initialize with $dateTime.
     *
     * @param \DateTime|null $dateTime Date/Time as a DateTime object
     */
    public function __construct(DateTime $dateTime = null)
    {
        $this->value = $dateTime;
    }

    /**
     * Creates a Value from the given $dateString.
     *
     * @param string $dateString
     *
     * @return \Ibexa\Core\FieldType\DateAndTime\Value
     */
    public static function fromString($dateString)
    {
        try {
            return new static(new DateTime($dateString));
        } catch (Exception $e) {
            throw new InvalidArgumentValue('$dateString', $dateString, __CLASS__, $e);
        }
    }

    /**
     * Creates a Value from the given $timestamp.
     *
     * @param int $timestamp
     *
     * @return \Ibexa\Core\FieldType\DateAndTime\Value
     */
    public static function fromTimestamp($timestamp)
    {
        try {
            return new static(new DateTime("@{$timestamp}"));
        } catch (Exception $e) {
            throw new InvalidArgumentValue('$timestamp', $timestamp, __CLASS__, $e);
        }
    }

    public function __toString()
    {
        if (!$this->value instanceof DateTime) {
            return '';
        }

        return $this->value->format($this->stringFormat);
    }
}

class_alias(Value::class, 'eZ\Publish\Core\FieldType\DateAndTime\Value');
