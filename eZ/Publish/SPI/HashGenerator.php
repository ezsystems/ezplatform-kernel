<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\SPI;

interface HashGenerator
{
    /**
     * Generates the hash.
     *
     * @return string
     */
    public function generate();
}
