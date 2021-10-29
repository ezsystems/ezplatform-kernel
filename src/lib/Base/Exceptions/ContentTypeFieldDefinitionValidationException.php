<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Base\Exceptions;

use Ibexa\Contracts\Core\Repository\Exceptions\ContentTypeFieldDefinitionValidationException as APIContentTypeFieldDefinitionValidationException;
use Ibexa\Core\Base\Translatable;
use Ibexa\Core\Base\TranslatableBase;

/**
 * This Exception is thrown on create or update content one or more given fields are not valid.
 */
class ContentTypeFieldDefinitionValidationException extends APIContentTypeFieldDefinitionValidationException implements Translatable
{
    use TranslatableBase;

    /**
     * Contains an array of field ValidationError objects indexed with FieldDefinition id and language code.
     *
     * Example:
     * <code>
     *  $fieldErrors = $exception->getFieldErrors();
     *  $fieldErrors["43"]["eng-GB"]->getTranslatableMessage();
     * </code>
     *
     * @var \Ibexa\Core\FieldType\ValidationError[]
     */
    protected $errors;

    /**
     * Generates: Content fields did not validate.
     *
     * Also sets the given $fieldErrors to the internal property, retrievable by getFieldErrors()
     *
     * @param \Ibexa\Core\FieldType\ValidationError[] $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
        $this->setMessageTemplate('Content Type Field definitions did not validate');
        parent::__construct($this->getBaseTranslation());
    }

    /**
     * Returns an array of field validation error messages.
     *
     * @return \Ibexa\Core\FieldType\ValidationError[]
     */
    public function getFieldErrors()
    {
        return $this->errors;
    }
}

class_alias(ContentTypeFieldDefinitionValidationException::class, 'eZ\Publish\Core\Base\Exceptions\ContentTypeFieldDefinitionValidationException');
