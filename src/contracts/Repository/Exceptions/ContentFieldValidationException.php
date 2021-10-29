<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Exceptions;

/**
 * This Exception is thrown on create or update content one or more given fields are not valid.
 */
abstract class ContentFieldValidationException extends ForbiddenException
{
    /**
     * Returns an array of field validation error messages.
     *
     * @return array
     */
    abstract public function getFieldErrors();
}

class_alias(ContentFieldValidationException::class, 'eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException');
