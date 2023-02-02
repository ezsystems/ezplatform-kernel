<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\MVC\Symfony;

use JsonSerializable;

final class SiteAccessGroup implements JsonSerializable
{
    /** @var string */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return array{'name': string}
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
