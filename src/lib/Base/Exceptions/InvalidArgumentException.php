<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Base\Exceptions;

use Exception;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException as APIInvalidArgumentException;
use Ibexa\Core\Base\Translatable;
use Ibexa\Core\Base\TranslatableBase;

/**
 * Invalid Argument Type Exception implementation.
 *
 * Usage: throw new InvalidArgumentException( 'nodes', 'array' );
 */
class InvalidArgumentException extends APIInvalidArgumentException implements Translatable
{
    use TranslatableBase;

    /**
     * Generates: "Argument '{$argumentName}' is invalid: {$whatIsWrong}".
     *
     * @param string $argumentName
     * @param string $whatIsWrong
     * @param \Exception|null $previous
     */
    public function __construct($argumentName, $whatIsWrong, Exception $previous = null)
    {
        $this->setMessageTemplate("Argument '%argumentName%' is invalid: %whatIsWrong%");
        $this->setParameters(['%argumentName%' => $argumentName, '%whatIsWrong%' => $whatIsWrong]);
        parent::__construct($this->getBaseTranslation(), 0, $previous);
    }
}

class_alias(InvalidArgumentException::class, 'eZ\Publish\Core\Base\Exceptions\InvalidArgumentException');
