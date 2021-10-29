<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\IO\Values;

/**
 * Override of BinaryFile that indicates a non existing file.
 *
 * Used for tolerance of var dir that does not match the database's content.
 */
class MissingBinaryFile extends BinaryFile
{
}

class_alias(MissingBinaryFile::class, 'eZ\Publish\Core\IO\Values\MissingBinaryFile');
