<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\IO\Values;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * Create struct for BinaryFile objects.
 */
class BinaryFileCreateStruct extends ValueObject
{
    /**
     * URI the binary file should be stored to.
     *
     * @var string
     */
    public $id;

    /**
     * The size of the file.
     *
     * @var int
     */
    public $size;

    /**
     * the input stream.
     *
     * @var resource
     */
    public $inputStream;

    /**
     * The file's mime type
     * If not provided, will be auto-detected by the IOService
     * Example: text/xml.
     *
     * @var string
     */
    public $mimeType;
}

class_alias(BinaryFileCreateStruct::class, 'eZ\Publish\Core\IO\Values\BinaryFileCreateStruct');
