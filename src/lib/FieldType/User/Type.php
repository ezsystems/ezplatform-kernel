<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\User;

use DateTimeImmutable;
use DateTimeInterface;
use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\User\Handler as SPIUserHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\PasswordHashService;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Core\FieldType\Value as BaseValue;
use Ibexa\Core\Repository\User\PasswordValidatorInterface;
use LogicException;

/**
 * The User field type.
 *
 * This field type represents a simple string.
 */
class Type extends FieldType
{
    public const PASSWORD_TTL_SETTING = 'PasswordTTL';
    public const PASSWORD_TTL_WARNING_SETTING = 'PasswordTTLWarning';
    public const REQUIRE_UNIQUE_EMAIL = 'RequireUniqueEmail';
    public const USERNAME_PATTERN = 'UsernamePattern';

    /** @var array */
    protected $settingsSchema = [
        self::PASSWORD_TTL_SETTING => [
            'type' => 'int',
            'default' => null,
        ],
        self::PASSWORD_TTL_WARNING_SETTING => [
            'type' => 'int',
            'default' => null,
        ],
        self::REQUIRE_UNIQUE_EMAIL => [
            'type' => 'bool',
            'default' => true,
        ],
        self::USERNAME_PATTERN => [
            'type' => 'string',
            'default' => '^[^@]+$',
        ],
    ];

    /** @var array */
    protected $validatorConfigurationSchema = [
        'PasswordValueValidator' => [
            'requireAtLeastOneUpperCaseCharacter' => [
                'type' => 'int',
                'default' => 1,
            ],
            'requireAtLeastOneLowerCaseCharacter' => [
                'type' => 'int',
                'default' => 1,
            ],
            'requireAtLeastOneNumericCharacter' => [
                'type' => 'int',
                'default' => 1,
            ],
            'requireAtLeastOneNonAlphanumericCharacter' => [
                'type' => 'int',
                'default' => null,
            ],
            'requireNewPassword' => [
                'type' => 'int',
                'default' => null,
            ],
            'minLength' => [
                'type' => 'int',
                'default' => 10,
            ],
        ],
    ];

    /** @var \Ibexa\Contracts\Core\Persistence\User\Handler */
    private $userHandler;

    /** @var \Ibexa\Contracts\Core\Repository\PasswordHashService */
    private $passwordHashService;

    /** @var \Ibexa\Core\Repository\User\PasswordValidatorInterface */
    private $passwordValidator;

    public function __construct(
        SPIUserHandler $userHandler,
        PasswordHashService $passwordHashGenerator,
        PasswordValidatorInterface $passwordValidator
    ) {
        $this->userHandler = $userHandler;
        $this->passwordHashService = $passwordHashGenerator;
        $this->passwordValidator = $passwordValidator;
    }

    /**
     * Returns the field type identifier for this field type.
     *
     * @return string
     */
    public function getFieldTypeIdentifier()
    {
        return 'ezuser';
    }

    /**
     * @param \Ibexa\Core\FieldType\User\Value|\Ibexa\Contracts\Core\FieldType\Value $value
     */
    public function getName(SPIValue $value, FieldDefinition $fieldDefinition, string $languageCode): string
    {
        return (string)$value->login;
    }

    /**
     * Indicates if the field definition of this type can appear only once in the same ContentType.
     *
     * @return bool
     */
    public function isSingular()
    {
        return true;
    }

    /**
     * Indicates if the field definition of this type can be added to a ContentType with Content instances.
     *
     * @return bool
     */
    public function onlyEmptyInstance()
    {
        return true;
    }

    /**
     * Returns the fallback default value of field type when no such default
     * value is provided in the field definition in content types.
     *
     * @return \Ibexa\Core\FieldType\User\Value
     */
    public function getEmptyValue()
    {
        return new Value();
    }

    /**
     * Inspects given $inputValue and potentially converts it into a dedicated value object.
     *
     * @param array|\Ibexa\Core\FieldType\User\Value $inputValue
     *
     * @return \Ibexa\Core\FieldType\User\Value The potentially converted and structurally plausible value.
     */
    protected function createValueFromInput($inputValue)
    {
        if (is_array($inputValue)) {
            $inputValue = $this->fromHash($inputValue);
        }

        return $inputValue;
    }

    /**
     * Throws an exception if value structure is not of expected format.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If the value does not match the expected structure.
     *
     * @param \Ibexa\Core\FieldType\User\Value $value
     */
    protected function checkValueStructure(BaseValue $value)
    {
        // Does nothing
    }

    /**
     * {@inheritdoc}
     */
    protected function getSortInfo(BaseValue $value)
    {
        return false;
    }

    /**
     * Converts an $hash to the Value defined by the field type.
     *
     * @param mixed $hash
     *
     * @return \Ibexa\Core\FieldType\User\Value $value
     */
    public function fromHash($hash)
    {
        if ($hash === null) {
            return $this->getEmptyValue();
        }

        if (isset($hash['passwordUpdatedAt']) && $hash['passwordUpdatedAt'] !== null) {
            $hash['passwordUpdatedAt'] = new DateTimeImmutable('@' . $hash['passwordUpdatedAt']);
        }

        return new Value($hash);
    }

    /**
     * Converts a $Value to a hash.
     *
     * @param \Ibexa\Core\FieldType\User\Value $value
     *
     * @return mixed
     */
    public function toHash(SPIValue $value)
    {
        if ($this->isEmptyValue($value)) {
            return null;
        }

        $hash = (array)$value;
        if ($hash['passwordUpdatedAt'] instanceof DateTimeInterface) {
            $hash['passwordUpdatedAt'] = $hash['passwordUpdatedAt']->getTimestamp();
        }

        return $hash;
    }

    public function toPersistenceValue(SPIValue $value)
    {
        $value->passwordHashType = $this->getPasswordHashTypeForPersistenceValue($value);
        if ($value->plainPassword) {
            $value->passwordHash = $this->passwordHashService->createPasswordHash(
                $value->plainPassword,
                $value->passwordHashType
            );
            $value->passwordUpdatedAt = new DateTimeImmutable();
        }

        return new FieldValue(
            [
                'data' => null,
                'externalData' => $this->toHash($value),
                'sortKey' => null,
            ]
        );
    }

    private function getPasswordHashTypeForPersistenceValue(SPIValue $value): int
    {
        if (null === $value->passwordHashType) {
            return $this->passwordHashService->getDefaultHashType();
        }

        if (!$this->passwordHashService->isHashTypeSupported($value->passwordHashType)) {
            return $this->passwordHashService->getDefaultHashType();
        }

        return $value->passwordHashType;
    }

    /**
     * Converts a persistence $fieldValue to a Value.
     *
     * This method builds a field type value from the $data and $externalData properties.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\FieldValue $fieldValue
     *
     * @return \Ibexa\Core\FieldType\User\Value
     */
    public function fromPersistenceValue(FieldValue $fieldValue)
    {
        return $this->acceptValue($fieldValue->externalData);
    }

    /**
     * Validates a field based on the validators in the field definition.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition $fieldDefinition The field definition of the field
     * @param \Ibexa\Core\FieldType\User\Value $fieldValue The field value for which an action is performed
     *
     * @return \Ibexa\Contracts\Core\FieldType\ValidationError[]
     */
    public function validate(FieldDefinition $fieldDefinition, SPIValue $fieldValue)
    {
        $errors = [];

        if ($this->isEmptyValue($fieldValue)) {
            return $errors;
        }

        if (!is_string($fieldValue->login) || empty($fieldValue->login)) {
            $errors[] = new ValidationError(
                'Login required',
                null,
                [],
                'username'
            );
        }

        $pattern = sprintf('/%s/', $fieldDefinition->fieldSettings[self::USERNAME_PATTERN]);
        $loginFormatValid = preg_match($pattern, $fieldValue->login);
        if (!$fieldValue->hasStoredLogin && !$loginFormatValid) {
            $errors[] = new ValidationError(
                'Invalid login format',
                null,
                [],
                'username'
            );
        }

        if (!is_string($fieldValue->email) || empty($fieldValue->email)) {
            $errors[] = new ValidationError(
                'Email required',
                null,
                [],
                'email'
            );
        } elseif (false === filter_var($fieldValue->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = new ValidationError(
                "The given e-mail '%email%' is invalid",
                null,
                ['%email%' => $fieldValue->email],
                'email'
            );
        }

        if (!$fieldValue->hasStoredLogin && (!is_string($fieldValue->plainPassword) || empty($fieldValue->plainPassword))) {
            $errors[] = new ValidationError(
                'Password required',
                null,
                [],
                'password'
            );
        }

        if (!is_bool($fieldValue->enabled)) {
            $errors[] = new ValidationError(
                'Enabled must be boolean value',
                null,
                [],
                'enabled'
            );
        }

        if (!$fieldValue->hasStoredLogin && isset($fieldValue->login)) {
            try {
                $login = $fieldValue->login;
                $this->userHandler->loadByLogin($login);

                // If you want to change this ValidationError message, please remember to change it also in Content Forms in lib/Validator/Constraints/FieldValueValidatorMessages class
                $errors[] = new ValidationError(
                    "The user login '%login%' is used by another user. You must enter a unique login.",
                    null,
                    [
                        '%login%' => $login,
                    ],
                    'username'
                );
            } catch (NotFoundException $e) {
                // Do nothing
            }
        }

        if ($fieldDefinition->fieldSettings[self::REQUIRE_UNIQUE_EMAIL]) {
            try {
                $email = $fieldValue->email;
                try {
                    $user = $this->userHandler->loadByEmail($email);
                } catch (LogicException $exception) {
                    // There are multiple users with the same email
                }

                // Don't prevent email update
                if (empty($user) || $user->id != $fieldValue->contentId) {
                    // If you want to change this ValidationError message, please remember to change it also in Content Forms in lib/Validator/Constraints/FieldValueValidatorMessages class
                    $errors[] = new ValidationError(
                        "Email '%email%' is used by another user. You must enter a unique email.",
                        null,
                        [
                            '%email%' => $email,
                        ],
                        'email'
                    );
                }
            } catch (NotFoundException $e) {
                // Do nothing
            }
        }

        if (!empty($fieldValue->plainPassword)) {
            $passwordValidationErrors = $this->passwordValidator->validatePassword(
                $fieldValue->plainPassword,
                $fieldDefinition
            );

            $errors = array_merge($errors, $passwordValidationErrors);

            if (!empty($fieldValue->passwordHash) && $this->isNewPasswordRequired($fieldDefinition)) {
                $isPasswordReused = $this->passwordHashService->isValidPassword(
                    $fieldValue->plainPassword,
                    $fieldValue->passwordHash,
                    $fieldValue->passwordHashType
                );

                if ($isPasswordReused) {
                    $errors[] = new ValidationError('New password cannot be the same as old password', null, [], 'password');
                }
            }
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function validateValidatorConfiguration($validatorConfiguration)
    {
        $validationErrors = [];

        foreach ((array)$validatorConfiguration as $validatorIdentifier => $constraints) {
            if ($validatorIdentifier !== 'PasswordValueValidator') {
                $validationErrors[] = new ValidationError(
                    "Validator '%validator%' is unknown",
                    null,
                    [
                        'validator' => $validatorIdentifier,
                    ],
                    "[$validatorIdentifier]"
                );
            }
        }

        return $validationErrors;
    }

    /**
     * {@inheritdoc}
     */
    public function validateFieldSettings($fieldSettings)
    {
        $validationErrors = [];

        foreach ($fieldSettings as $name => $value) {
            if (!isset($this->settingsSchema[$name])) {
                $validationErrors[] = new ValidationError(
                    "Setting '%setting%' is unknown",
                    null,
                    [
                        '%setting%' => $name,
                    ],
                    "[$name]"
                );

                continue;
            }

            $error = null;
            switch ($name) {
                case self::PASSWORD_TTL_SETTING:
                    $error = $this->validatePasswordTTLSetting($name, $value);
                    break;
                case self::PASSWORD_TTL_WARNING_SETTING:
                    $error = $this->validatePasswordTTLWarningSetting($name, $value, $fieldSettings);
                    break;
            }

            if ($error !== null) {
                $validationErrors[] = $error;
            }
        }

        return $validationErrors;
    }

    private function validatePasswordTTLSetting(string $name, $value): ?ValidationError
    {
        if ($value !== null && !is_int($value)) {
            return new ValidationError(
                "Setting '%setting%' value must be of integer type",
                null,
                [
                    '%setting%' => $name,
                ],
                "[$name]"
            );
        }

        return null;
    }

    private function validatePasswordTTLWarningSetting(string $name, $value, $fieldSettings): ?ValidationError
    {
        if ($value !== null) {
            if (!is_int($value)) {
                return new ValidationError(
                    "Setting '%setting%' value must be of integer type",
                    null,
                    [
                        '%setting%' => $name,
                    ],
                    "[$name]"
                );
            }

            if ($value > 0) {
                $passwordTTL = $fieldSettings[self::PASSWORD_TTL_SETTING] ?? null;
                if ($value >= (int)$passwordTTL) {
                    return new ValidationError(
                        'Password expiration warning value should be lower then password expiration value',
                        null,
                        [],
                        "[$name]"
                    );
                }
            }
        }

        return null;
    }

    private function isNewPasswordRequired(FieldDefinition $fieldDefinition): bool
    {
        $isExplicitRequired = $fieldDefinition->validatorConfiguration['PasswordValueValidator']['requireNewPassword'] ?? false;
        if ($isExplicitRequired) {
            return true;
        }

        return $this->isPasswordTTLEnabled($fieldDefinition);
    }

    private function isPasswordTTLEnabled(FieldDefinition $fieldDefinition): bool
    {
        return ($fieldDefinition->fieldSettings[self::PASSWORD_TTL_SETTING] ?? null) > 0;
    }
}

class_alias(Type::class, 'eZ\Publish\Core\FieldType\User\Type');
