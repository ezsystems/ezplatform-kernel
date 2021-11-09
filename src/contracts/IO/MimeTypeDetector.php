<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\IO;

interface MimeTypeDetector
{
    /**
     * Returns the MIME type of the file identified by $path.
     *
     * @param string $path
     *
     * @return string
     */
    public function getFromPath($path);

    /**
     * Returns the MIME type of the data in $buffer.
     *
     * @param string $buffer
     *
     * @return string
     */
    public function getFromBuffer($buffer);
}

class_alias(MimeTypeDetector::class, 'eZ\Publish\SPI\IO\MimeTypeDetector');
