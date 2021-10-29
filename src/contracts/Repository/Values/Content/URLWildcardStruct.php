<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class URLWildcardStruct extends ValueObject
{
    /** @var string */
    public $destinationUrl;

    /** @var string */
    public $sourceUrl;

    /** @var bool */
    public $forward;
}

class_alias(URLWildcardStruct::class, 'eZ\Publish\API\Repository\Values\Content\URLWildcardStruct');
