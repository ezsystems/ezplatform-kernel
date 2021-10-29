<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Base\Exceptions;

use Ibexa\Contracts\Core\Repository\Exceptions\LimitationValidationException as APILimitationValidationException;
use Ibexa\Core\Base\Translatable;
use Ibexa\Core\Base\TranslatableBase;

/**
 * This Exception is thrown on create, update or assign policy or role
 * when one or more given limitations are not valid.
 */
class LimitationValidationException extends APILimitationValidationException implements Translatable
{
    use TranslatableBase;

    /**
     * Contains an array of limitation ValidationError objects.
     *
     * @var \Ibexa\Core\FieldType\ValidationError[]
     */
    protected $errors;

    /**
     * Generates: Limitations did not validate.
     *
     * Also sets the given $errors to the internal property, retrievable by getValidationErrors()
     *
     * @param \Ibexa\Core\FieldType\ValidationError[] $errors
     */
    public function __construct(array $errors)
    {
        $this->validationErrors = $errors;
        $this->setMessageTemplate('Limitations did not validate');
        parent::__construct($this->getBaseTranslation());
    }

    /**
     * Returns an array of limitation ValidationError objects.
     *
     * @return \Ibexa\Core\FieldType\ValidationError[]
     */
    public function getLimitationErrors()
    {
        return $this->errors;
    }
}

class_alias(LimitationValidationException::class, 'eZ\Publish\Core\Base\Exceptions\LimitationValidationException');
