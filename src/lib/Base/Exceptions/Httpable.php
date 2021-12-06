<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Base\Exceptions;

/**
 * Interface for exceptions that maps to http status codes.
 *
 * The constants must be used as error code for this to be usable
 */
interface Httpable
{
    public const BAD_REQUEST = 400;
    public const UNAUTHORIZED = 401;
    public const PAYMENT_REQUIRED = 402;
    public const FORBIDDEN = 403;
    public const NOT_FOUND = 404;
    public const METHOD_NOT_ALLOWED = 405;
    public const NOT_ACCEPTABLE = 406;
    public const CONFLICT = 409;
    public const GONE = 410;

    public const UNSUPPORTED_MEDIA_TYPE = 415;

    public const INTERNAL_ERROR = 500;
    public const NOT_IMPLEMENTED = 501;
    public const SERVICE_UNAVAILABLE = 503;
}

class_alias(Httpable::class, 'eZ\Publish\Core\Base\Exceptions\Httpable');
