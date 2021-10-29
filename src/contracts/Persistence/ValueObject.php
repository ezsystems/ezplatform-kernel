<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Persistence;

use Ibexa\Contracts\Core\Repository\Values\ValueObject as APIValueObject;

/**
 * Base SPI Value object.
 *
 * All properties of SPI\ValueObject *must* be serializable for cache & NoSQL use.
 */
abstract class ValueObject extends APIValueObject
{
}

class_alias(ValueObject::class, 'eZ\Publish\SPI\Persistence\ValueObject');
