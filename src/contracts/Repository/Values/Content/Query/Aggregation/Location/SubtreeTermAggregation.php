<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Location;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractTermAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\LocationAggregation;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

final class SubtreeTermAggregation extends AbstractTermAggregation implements LocationAggregation
{
    /** @var string */
    private $pathString;

    public function __construct(string $name, string $pathString)
    {
        parent::__construct($name);

        if (!$this->isValidPathString($pathString)) {
            throw new InvalidArgumentException(
                '$pathString',
                "'$pathString' value must follow the path string format, e.g. /1/2/"
            );
        }

        $this->pathString = $pathString;
    }

    public function getPathString(): string
    {
        return $this->pathString;
    }

    private function isValidPathString(string $pathString): bool
    {
        return preg_match('/^(\/\w+)+\/$/', $pathString) === 1;
    }

    public static function fromLocation(string $name, Location $location): self
    {
        return new self($name, $location->pathString);
    }
}

class_alias(SubtreeTermAggregation::class, 'eZ\Publish\API\Repository\Values\Content\Query\Aggregation\Location\SubtreeTermAggregation');
