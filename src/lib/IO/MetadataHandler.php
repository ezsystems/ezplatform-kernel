<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\IO;

/**
 * @deprecated Not in use anymore by the kernel.
 */
interface MetadataHandler
{
    /**
     * Extracts metadata for the file identified by $path.
     *
     * @param string $path
     *
     * @return array Metadata hash
     */
    public function extract($path);
}

class_alias(MetadataHandler::class, 'eZ\Publish\Core\IO\MetadataHandler');
