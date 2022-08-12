<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Base\Exceptions;

use eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException as APIContentFieldValidationException;
use eZ\Publish\Core\Base\Translatable;
use eZ\Publish\Core\Base\TranslatableBase;
use eZ\Publish\Core\FieldType\ValidationError;

/**
 * This Exception is thrown on create or update content one or more given fields are not valid.
 */
class ContentFieldValidationException extends APIContentFieldValidationException implements Translatable
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
     * @var array<array-key, array<string, \eZ\Publish\Core\FieldType\ValidationError>>
     */
    protected $errors;

    /** @var string|null */
    protected $target;

    /**
     * Generates: Content fields did not validate.
     *
     * Also sets the given $fieldErrors to the internal property, retrievable by getFieldErrors()
     *
     * @param array<array-key, array<string, \eZ\Publish\Core\FieldType\ValidationError>> $errors
     */
    public function __construct(array $errors, ?string $target = null)
    {
        $this->errors = $errors;
        $this->target = $target;

        $this->setMessageTemplate('Content Fields %contentInfo%did not validate: %errors%');
        $this->setParameters([
            '%errors%' => $this->generateValidationErrorsMessages(),
            '%contentInfo%' => $this->target !== null ? sprintf('of Content "%s" ', $this->target) : '',
        ]);

        parent::__construct($this->getBaseTranslation());
    }

    /**
     * Returns an array of field validation error messages.
     *
     * @return array<array-key, array<string, \eZ\Publish\Core\FieldType\ValidationError>>
     */
    public function getFieldErrors()
    {
        return $this->errors;
    }

    private function generateValidationErrorsMessages(): string
    {
        $message = '';
        foreach ($this->getFieldErrors() as $validationErrors) {
            foreach ($validationErrors as $validationError) {
                if (is_array($validationError)) {
                    foreach ($validationError as $item) {
                        $message .= $this->generateValidationErrorMessage($item);
                    }
                } else {
                    $message .= $this->generateValidationErrorMessage($validationError);
                }
            }
        }

        return $message;
    }

    private function generateValidationErrorMessage(ValidationError $validationError): string
    {
        return sprintf("\n- %s", $validationError->getTranslatableMessage());
    }
}
