<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Values\Content;

use ArrayIterator;
use Countable;
use Iterator;
use IteratorAggregate;

/**
 * VO representing materialized path of the location entry, eg: /1/2/.
 */
final class LocationPath implements IteratorAggregate, Countable
{
    private const SEGMENT_SEPARATOR = '/';

    /** @var int[] */
    private $segments;

    public function __construct(array $segments = [])
    {
        $this->segments = $segments;
    }

    public function slice(int $start, ?int $length = null): LocationPath
    {
        return new LocationPath(array_slice($this->segments, $start, $length));
    }

    public function append(int ...$segments): self
    {
        return new self(array_merge($this->segments, $segments));
    }

    public function prepend(int ...$segments): self
    {
        return new self(array_merge($segments, $this->segments));
    }

    public function getSegments(): array
    {
        return $this->segments;
    }

    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->segments);
    }

    public function count(): int
    {
        return count($this->segments);
    }

    public function __toString(): string
    {
        return implode(self::SEGMENT_SEPARATOR, $this->segments);
    }

    public static function fromString(string $path): self
    {
        return new self(explode(self::SEGMENT_SEPARATOR, trim($path, self::SEGMENT_SEPARATOR)));
    }
}
