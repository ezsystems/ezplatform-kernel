<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\Date;

use DateTime;
use Exception;
use Ibexa\Core\Base\Exceptions\InvalidArgumentValue;
use Ibexa\Core\FieldType\Value as BaseValue;

/**
 * Value for Date field type.
 */
class Value extends BaseValue
{
    /**
     * Date content.
     *
     * @var \DateTime|null
     */
    public $date;

    /**
     * Date format to be used by {@link __toString()}.
     *
     * @var string
     */
    public $stringFormat = 'l d F Y';

    /**
     * Construct a new Value object and initialize with $dateTime.
     *
     * @param \DateTime|null $dateTime Date as a DateTime object
     */
    public function __construct(DateTime $dateTime = null)
    {
        if ($dateTime !== null) {
            $dateTime = clone $dateTime;
            $dateTime->setTime(0, 0, 0);
        }
        $this->date = $dateTime;
    }

    /**
     * Creates a Value from the given $dateString.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     *
     * @param string $dateString
     *
     * @return \Ibexa\Core\FieldType\Date\Value
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
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     *
     * @param int $timestamp
     *
     * @return \Ibexa\Core\FieldType\Date\Value
     */
    public static function fromTimestamp($timestamp)
    {
        try {
            $datetime = new DateTime();
            $datetime->setTimestamp($timestamp);

            return new static($datetime);
        } catch (Exception $e) {
            throw new InvalidArgumentValue('$timestamp', $timestamp, __CLASS__, $e);
        }
    }

    public function __toString()
    {
        if (!$this->date instanceof DateTime) {
            return '';
        }

        return $this->date->format($this->stringFormat);
    }
}

class_alias(Value::class, 'eZ\Publish\Core\FieldType\Date\Value');
