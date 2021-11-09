<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Exceptions;

/**
 * This Exception is thrown on create or update content type when content type is not valid.
 */
abstract class ContentTypeValidationException extends ForbiddenException
{
}

class_alias(ContentTypeValidationException::class, 'eZ\Publish\API\Repository\Exceptions\ContentTypeValidationException');
