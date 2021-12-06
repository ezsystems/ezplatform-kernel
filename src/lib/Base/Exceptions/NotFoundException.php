<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Base\Exceptions;

use Exception;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException as APINotFoundException;
use Ibexa\Core\Base\Translatable;
use Ibexa\Core\Base\TranslatableBase;

/**
 * Not Found Exception implementation.
 *
 * Use:
 *   throw new NotFound( 'Content', 42 );
 */
class NotFoundException extends APINotFoundException implements Httpable, Translatable
{
    use TranslatableBase;

    /**
     * Generates: Could not find '{$what}' with identifier '{$identifier}'.
     *
     * @param string $what
     * @param mixed $identifier
     * @param \Exception|null $previous
     */
    public function __construct($what, $identifier, Exception $previous = null)
    {
        $identifierStr = is_string($identifier) ? $identifier : var_export($identifier, true);
        $this->setMessageTemplate("Could not find '%what%' with identifier '%identifier%'");
        $this->setParameters(['%what%' => $what, '%identifier%' => $identifierStr]);
        parent::__construct($this->getBaseTranslation(), self::NOT_FOUND, $previous);
    }
}

class_alias(NotFoundException::class, 'eZ\Publish\Core\Base\Exceptions\NotFoundException');
