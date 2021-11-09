<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\IO\MetadataHandler;

use Ibexa\Core\IO\MetadataHandler;

/**
 * @deprecated Not in use anymore by the kernel.
 */
class ImageSize implements MetadataHandler
{
    public function extract($filePath)
    {
        $metadata = getimagesize($filePath);

        return [
            'width' => $metadata[0],
            'height' => $metadata[1],
            // required until a dedicated mimetype metadata handler is added
            'mime' => $metadata['mime'],
        ];
    }
}

class_alias(ImageSize::class, 'eZ\Publish\Core\IO\MetadataHandler\ImageSize');
