<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Base\Exceptions;

use Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException as APIForbiddenException;
use Ibexa\Core\Base\Translatable;
use Ibexa\Core\Base\TranslatableBase;

/**
 * Forbidden Exception implementation.
 */
class ForbiddenException extends APIForbiddenException implements Translatable
{
    use TranslatableBase;

    /**
     * @param string $messageTemplate The message template, with placeholders for parameters.
     *                                E.g. "Content with ID %contentId% could not be found".
     * @param array $parameters Hash map with param placeholder as key and its corresponding value.
     *                          E.g. array('%contentId%' => 123).
     */
    public function __construct($messageTemplate, array $parameters = [])
    {
        /** @Ignore */
        $this->setMessageTemplate($messageTemplate);
        $this->setParameters($parameters);

        parent::__construct($this->getBaseTranslation());
    }
}

class_alias(ForbiddenException::class, 'eZ\Publish\Core\Base\Exceptions\ForbiddenException');
