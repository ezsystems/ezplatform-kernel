<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\Time;

use DateTime;
use Exception;
use Ibexa\Core\Base\Exceptions\InvalidArgumentValue;
use Ibexa\Core\FieldType\Value as BaseValue;

/**
 * Value for Time field type.
 */
class Value extends BaseValue
{
    /**
     * Time of day as number of seconds.
     *
     * @var int|null
     */
    public $time;

    /**
     * Time format to be used by {@link __toString()}.
     *
     * @var string
     */
    public $stringFormat = 'H:i:s';

    /**
     * Construct a new Value object and initialize it with $seconds as number of seconds from beginning of day.
     *
     * @param mixed $seconds
     */
    public function __construct($seconds = null)
    {
        $this->time = $seconds;
    }

    /**
     * Creates a Value from the given $dateTime.
     *
     * @param \DateTime $dateTime
     *
     * @return \Ibexa\Core\FieldType\Time\Value
     */
    public static function fromDateTime(DateTime $dateTime)
    {
        $dateTime = clone $dateTime;

        return new static($dateTime->getTimestamp() - $dateTime->setTime(0, 0, 0)->getTimestamp());
    }

    /**
     * Creates a Value from the given $timeString.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     *
     * @param string $timeString
     *
     * @return \Ibexa\Core\FieldType\Time\Value
     */
    public static function fromString($timeString)
    {
        try {
            return static::fromDateTime(new DateTime($timeString));
        } catch (Exception $e) {
            throw new InvalidArgumentValue('$timeString', $timeString, __CLASS__, $e);
        }
    }

    /**
     * Creates a Value from the given $timestamp.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     *
     * @param int $timestamp
     *
     * @return static
     */
    public static function fromTimestamp($timestamp)
    {
        try {
            $dateTime = new DateTime("@{$timestamp}");

            return static::fromDateTime($dateTime);
        } catch (Exception $e) {
            throw new InvalidArgumentValue('$timestamp', $timestamp, __CLASS__, $e);
        }
    }

    public function __toString()
    {
        if ($this->time === null) {
            return '';
        }

        $dateTime = new DateTime("@{$this->time}");

        return $dateTime->format($this->stringFormat);
    }
}

class_alias(Value::class, 'eZ\Publish\Core\FieldType\Time\Value');
