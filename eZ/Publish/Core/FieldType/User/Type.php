<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\FieldType\User;

use DateTimeImmutable;
use DateTimeInterface;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\PasswordHashService;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\Core\FieldType\Value as BaseValue;
use eZ\Publish\Core\Repository\User\PasswordValidatorInterface;
use eZ\Publish\SPI\FieldType\Value as SPIValue;
use eZ\Publish\SPI\Persistence\Content\FieldValue;
use eZ\Publish\SPI\Persistence\User\Handler as SPIUserHandler;
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

    /** @var \eZ\Publish\SPI\Persistence\User\Handler */
    private $userHandler;

    /** @var \eZ\Publish\API\Repository\PasswordHashService */
    private $passwordHashService;

    /** @var \eZ\Publish\Core\Repository\User\PasswordValidatorInterface */
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
     * @param \eZ\Publish\Core\FieldType\User\Value|\eZ\Publish\SPI\FieldType\Value $value
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
     * @return \eZ\Publish\Core\FieldType\User\Value
     */
    public function getEmptyValue()
    {
        return new Value();
    }

    /**
     * Inspects given $inputValue and potentially converts it into a dedicated value object.
     *
     * @param array|\eZ\Publish\Core\FieldType\User\Value $inputValue
     *
     * @return \eZ\Publish\Core\FieldType\User\Value The potentially converted and structurally plausible value.
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
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException If the value does not match the expected structure.
     *
     * @param \eZ\Publish\Core\FieldType\User\Value $value
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
     * @return \eZ\Publish\Core\FieldType\User\Value $value
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
     * @param \eZ\Publish\Core\FieldType\User\Value $value
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

    /**
     * Converts a $value to a persistence value.
     *
     * In this method the field type puts the data which is stored in the field of content in the repository
     * into the property FieldValue::data. The format of $data is a primitive, an array (map) or an object, which
     * is then canonically converted to e.g. json/xml structures by future storage engines without
     * further conversions. For mapping the $data to the legacy database an appropriate Converter
     * (implementing eZ\Publish\Core\Persistence\Legacy\FieldValue\Converter) has implemented for the field
     * type. Note: $data should only hold data which is actually stored in the field. It must not
     * hold data which is stored externally.
     *
     * The $externalData property in the FieldValue is used for storing data externally by the
     * FieldStorage interface method storeFieldData.
     *
     * The FieldValuer::sortKey is build by the field type for using by sort operations.
     *
     * @see \eZ\Publish\SPI\Persistence\Content\FieldValue
     *
     * @param \eZ\Publish\Core\FieldType\User\Value $value The value of the field type
     *
     * @return \eZ\Publish\SPI\Persistence\Content\FieldValue the value processed by the storage engine
     */
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
     * @param \eZ\Publish\SPI\Persistence\Content\FieldValue $fieldValue
     *
     * @return \eZ\Publish\Core\FieldType\User\Value
     */
    public function fromPersistenceValue(FieldValue $fieldValue)
    {
        return $this->acceptValue($fieldValue->externalData);
    }

    /**
     * Validates a field based on the validators in the field definition.
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $fieldDefinition The field definition of the field
     * @param \eZ\Publish\Core\FieldType\User\Value $fieldValue The field value for which an action is performed
     *
     * @return \eZ\Publish\SPI\FieldType\ValidationError[]
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
