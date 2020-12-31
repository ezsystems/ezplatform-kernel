<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Bundle\EzPublishCoreBundle\Command;

interface BackwardCompatibleCommand
{
    /**
     * Returns deprecated command aliases.
     *
     * @return string[]
     */
    public function getDeprecatedAliases(): array;
}
