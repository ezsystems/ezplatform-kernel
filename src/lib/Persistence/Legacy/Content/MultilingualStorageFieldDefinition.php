<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Content;

use Ibexa\Contracts\Core\Persistence\ValueObject;

class MultilingualStorageFieldDefinition extends ValueObject
{
    /** @var string */
    public $name;

    /** @var string */
    public $description;

    /** @var string */
    public $dataText;

    /** @var string */
    public $dataJson;

    /** @var int */
    public $languageId;
}

class_alias(MultilingualStorageFieldDefinition::class, 'eZ\Publish\Core\Persistence\Legacy\Content\MultilingualStorageFieldDefinition');
