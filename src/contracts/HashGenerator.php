<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core;

interface HashGenerator
{
    /**
     * Generates the hash.
     *
     * @return string
     */
    public function generate();
}

class_alias(HashGenerator::class, 'eZ\Publish\SPI\HashGenerator');
