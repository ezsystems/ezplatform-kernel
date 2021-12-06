<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\ObjectState;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * This class represents a value for updating object states.
 */
class ObjectStateUpdateStruct extends ValueObject
{
    /**
     * Readable unique string identifier of a group.
     *
     * @var string
     */
    public $identifier;

    /**
     * The default language code.
     *
     * @var string
     */
    public $defaultLanguageCode;

    /**
     * An array of names with languageCode keys.
     *
     * @var string[]
     */
    public $names;

    /**
     * An array of descriptions with languageCode keys.
     *
     * @var string[]
     */
    public $descriptions;
}

class_alias(ObjectStateUpdateStruct::class, 'eZ\Publish\API\Repository\Values\ObjectState\ObjectStateUpdateStruct');
