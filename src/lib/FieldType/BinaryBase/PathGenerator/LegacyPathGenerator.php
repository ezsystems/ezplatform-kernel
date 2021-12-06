<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\BinaryBase\PathGenerator;

use Ibexa\Contracts\Core\FieldType\BinaryBase\PathGenerator;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;

class LegacyPathGenerator extends PathGenerator
{
    public function getStoragePathForField(Field $field, VersionInfo $versionInfo)
    {
        $extension = pathinfo($field->value->externalData['fileName'], PATHINFO_EXTENSION);

        return $this->getFirstPartOfMimeType($field->value->externalData['mimeType'])
            . '/' . bin2hex(random_bytes(16))
            . (!empty($extension) ? '.' . $extension : '');
    }

    /**
     * Extracts the first part (before the '/') from the given $mimeType.
     *
     * @param string $mimeType
     *
     * @return string
     */
    protected function getFirstPartOfMimeType($mimeType)
    {
        return substr($mimeType, 0, strpos($mimeType, '/'));
    }
}

class_alias(LegacyPathGenerator::class, 'eZ\Publish\Core\FieldType\BinaryBase\PathGenerator\LegacyPathGenerator');
