<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * This class represents a result of a translated url wildcard which is not an URLAlias.
 *
 * @property-read string $uri The found resource uri
 * @property-read bool $forward indicates if the url is redirected or not
 */
class URLWildcardTranslationResult extends ValueObject
{
    /**
     * The found resource uri.
     *
     * @var string
     */
    protected $uri;

    /**
     * Indicates if the url is redirected or not.
     *
     * @var bool
     */
    protected $forward;
}

class_alias(URLWildcardTranslationResult::class, 'eZ\Publish\API\Repository\Values\Content\URLWildcardTranslationResult');
