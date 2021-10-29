<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\IO;

/**
 * Resolves IO complex settings.
 *
 * @internal
 */
interface IOConfigProvider
{
    public function getRootDir(): string;

    public function getLegacyUrlPrefix(): string;

    public function getUrlPrefix(): string;
}

class_alias(IOConfigProvider::class, 'eZ\Publish\Core\IO\IOConfigProvider');
