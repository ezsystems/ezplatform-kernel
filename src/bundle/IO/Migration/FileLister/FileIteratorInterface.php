<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\IO\Migration\FileLister;

use Countable;
use Iterator;

/**
 * Iterates over BinaryFile id entries.
 */
interface FileIteratorInterface extends Countable, Iterator
{
}

class_alias(FileIteratorInterface::class, 'eZ\Bundle\EzPublishIOBundle\Migration\FileLister\FileIteratorInterface');
