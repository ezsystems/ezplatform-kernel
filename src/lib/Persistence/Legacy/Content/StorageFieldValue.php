<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Persistence\Legacy\Content;

use Ibexa\Contracts\Core\Persistence\ValueObject;

class StorageFieldValue extends ValueObject
{
    /**
     * Float data.
     *
     * @var float
     */
    public $dataFloat;

    /**
     * Integer data.
     *
     * @var int
     */
    public $dataInt;

    /**
     * Text data.
     *
     * @var string
     */
    public $dataText;

    /**
     * Integer sort key.
     *
     * @var int
     */
    public $sortKeyInt = 0;

    /**
     * Text sort key.
     *
     * @var string
     */
    public $sortKeyString = '';
}

class_alias(StorageFieldValue::class, 'eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue');
