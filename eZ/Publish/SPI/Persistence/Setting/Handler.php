<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\SPI\Persistence\Setting;

interface Handler
{
    public function create(
        string $group,
        string $identifier,
        string $serializedValue
    ): Setting;

    public function update(
        string $group,
        string $identifier,
        string $serializedValue
    ): Setting;

    public function load(
        string $group,
        string $identifier
    ): Setting;

    public function delete(
        string $group,
        string $identifier
    ): void;
}
