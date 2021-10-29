<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Options;

interface OptionsBag
{
    public function all(): array;

    /**
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public function get(string $key, $default = null);

    public function has(string $key): bool;
}

class_alias(OptionsBag::class, 'eZ\Publish\SPI\Options\OptionsBag');
