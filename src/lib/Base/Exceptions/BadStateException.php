<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Base\Exceptions;

use Exception;
use Ibexa\Contracts\Core\Repository\Exceptions\BadStateException as APIBadStateException;
use Ibexa\Core\Base\Translatable;
use Ibexa\Core\Base\TranslatableBase;

/**
 * BadState Exception implementation.
 *
 * Usage: throw new BadState( 'nodes', 'array' );
 */
class BadStateException extends APIBadStateException implements Translatable
{
    use TranslatableBase;

    /**
     * Generates: "Argument '{$argumentName}' has a bad state: {$whatIsWrong}".
     *
     * @param string $argumentName
     * @param string $whatIsWrong
     * @param \Exception|null $previous
     */
    public function __construct($argumentName, $whatIsWrong, Exception $previous = null)
    {
        $this->setMessageTemplate("Argument '%argumentName%' has a bad state: %whatIsWrong%");
        $this->setParameters(['%argumentName%' => $argumentName, '%whatIsWrong%' => $whatIsWrong]);
        parent::__construct($this->getBaseTranslation(), 0, $previous);
    }
}

class_alias(BadStateException::class, 'eZ\Publish\Core\Base\Exceptions\BadStateException');
