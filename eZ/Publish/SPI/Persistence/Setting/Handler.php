<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\SPI\Persistence\Setting;

interface Handler
{
    /**
     * @param $value mixed Any value to be serialized and stored.
     */
    public function create(
        string $group,
        string $identifier,
        $value
    ): Setting;

    /**
     * @param $value mixed Any value to be serialized and stored.
     */
    public function update(
        string $group,
        string $identifier,
        $value
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
