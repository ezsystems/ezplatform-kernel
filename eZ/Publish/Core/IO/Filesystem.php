<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\IO;

use League\Flysystem\Filesystem as FlysystemFilesystem;
use LogicException;

class Filesystem extends FlysystemFilesystem
{
    public function getMetadata($path)
    {
        $path = $this->normalizeRelativePath($path);
        $this->assertPresent($path);

        return $this->getAdapter()->getMetadata($path);
    }

    public function has($path): bool
    {
        $path = $this->normalizeRelativePath($path);

        return !(strlen($path) === 0) && (bool)$this->getAdapter()->has($path);
    }

    public function getMimetype($path)
    {
        $path = $this->normalizeRelativePath($path);
        $this->assertPresent($path);

        if ((!$object = $this->getAdapter()->getMimetype($path)) || !array_key_exists('mimetype', $object)) {
            return false;
        }

        return $object['mimetype'];
    }

    public function delete($path)
    {
        $path = $this->normalizeRelativePath($path);
        $this->assertPresent($path);

        return $this->getAdapter()->delete($path);
    }

    private function normalizeRelativePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $parts = [];

        foreach (explode('/', $path) as $part) {
            switch ($part) {
                case '':
                case '.':
                    break;

                case '..':
                    if (empty($parts)) {
                        throw new LogicException(
                            'Path is outside of the defined root, path: [' . $path . ']'
                        );
                    }
                    array_pop($parts);
                    break;

                default:
                    $parts[] = $part;
                    break;
            }
        }

        return implode('/', $parts);
    }
}
