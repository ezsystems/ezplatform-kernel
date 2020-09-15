<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\API\Repository\Tests\FieldType;

use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use eZ\Publish\API\Repository\Exceptions\BadStateException;
use eZ\Publish\API\Repository\Exceptions\ForbiddenException;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\User\Type;
use eZ\Publish\Core\FieldType\User\Value as UserValue;
use eZ\Publish\Core\Repository\Values\User\User;
use eZ\Publish\API\Repository\Values\Content\Field;

/**
 * Integration test for use field type.
 *
 * @group integration
 * @group field-type
 */
class UserIntegrationTest extends BaseIntegrationTest
{
    private const TEST_LOGIN = 'hans';

    /**
     * Get name of tested field type.
     *
     * @return string
     */
    public function getTypeName()
    {
        return 'ezuser';
    }

    /**
     * Get expected settings schema.
     *
     * @return array
     */
    public function getSettingsSchema()
    {
        return [
            'PasswordTTL' => [
                'type' => 'int',
                'default' => null,
            ],
            'PasswordTTLWarning' => [
                'type' => 'int',
                'default' => null,
            ],
            'RequireUniqueEmail' => [
                'type' => 'bool',
                'default' => true,
            ],
            'UsernamePattern' => [
                'type' => 'string',
                'default' => '^[^@]+$',
            ],
        ];
    }

    /**
     * Get a valid $fieldSettings value.
     *
     * @return mixed
     */
    public function getValidFieldSettings()
    {
        return [
            'PasswordTTL' => null,
            'PasswordTTLWarning' => null,
            'RequireUniqueEmail' => false,
            'UsernamePattern' => '.*',
        ];
    }

    /**
     * Get $fieldSettings value not accepted by the field type.
     *
     * @return mixed
     */
    public function getInvalidFieldSettings()
    {
        return [
            'somethingUnknown' => 0,
        ];
    }

    /**
     * Get expected validator schema.
     *
     * @return array
     */
    public function getValidatorSchema()
    {
        return [
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
    }

    /**
     * Get a valid $validatorConfiguration.
     *
     * @return mixed
     */
    public function getValidValidatorConfiguration()
    {
        return [
            'PasswordValueValidator' => [
                'requireAtLeastOneUpperCaseCharacter' => true,
                'requireAtLeastOneLowerCaseCharacter' => true,
                'requireAtLeastOneNumericCharacter' => true,
                'requireAtLeastOneNonAlphanumericCharacter' => false,
                'requireNewPassword' => false,
                'minLength' => 10,
            ],
        ];
    }

    /**
     * Get $validatorConfiguration not accepted by the field type.
     *
     * @return mixed
     */
    public function getInvalidValidatorConfiguration()
    {
        return [
            'unknown' => ['value' => 23],
        ];
    }

    /**
     * Get initial field externals data.
     *
     * @return \eZ\Publish\Core\FieldType\User\Value
     */
    public function getValidCreationFieldData(): UserValue
    {
        return new UserValue([
            'login' => self::TEST_LOGIN,
            'email' => sprintf('%s@example.com', self::TEST_LOGIN),
            'enabled' => true,
            'plainPassword' => 'PassWord42',
        ]);
    }

    /**
     * Get name generated by the given field type (via fieldType->getName()).
     *
     * @return string
     */
    public function getFieldName()
    {
        return self::TEST_LOGIN;
    }

    /**
     * Asserts that the field data was loaded correctly.
     *
     * Asserts that the data provided by {@link getValidCreationFieldData()}
     * was stored and loaded correctly.
     *
     * @param Field $field
     */
    public function assertFieldDataLoadedCorrect(Field $field)
    {
        $this->assertInstanceOf(
            UserValue::class,
            $field->value
        );

        $expectedData = [
            'hasStoredLogin' => true,
            'login' => self::TEST_LOGIN,
            'email' => 'hans@example.com',
            'passwordHashType' => User::PASSWORD_HASH_PHP_DEFAULT,
            'enabled' => true,
        ];

        $this->assertPropertiesCorrect(
            $expectedData,
            $field->value
        );

        $this->assertNotNull($field->value->contentId);
    }

    /**
     * Get field data which will result in errors during creation.
     *
     * This is a PHPUnit data provider.
     *
     * The returned records must contain of an error producing data value and
     * the expected exception class (from the API or SPI, not implementation
     * specific!) as the second element. For example:
     *
     * <code>
     * array(
     *      array(
     *          new DoomedValue( true ),
     *          'eZ\\Publish\\API\\Repository\\Exceptions\\ContentValidationException'
     *      ),
     *      // ...
     * );
     * </code>
     *
     * @return array[]
     */
    public function provideInvalidCreationFieldData()
    {
        return [];
    }

    public function testCreateContentFails(
        $failingValue = null,
        ?string $expectedException = null
    ): void {
        $this->markTestSkipped('Values are ignored on creation.');
    }

    /**
     * Get update field externals data.
     *
     * @return \eZ\Publish\Core\FieldType\User\Value
     */
    public function getValidUpdateFieldData()
    {
        return new UserValue(
            [
                'hasStoredLogin' => true,
                'login' => 'changeLogin',
                'email' => 'changeEmail@ez.no',
                'passwordHash' => '*2',
                'passwordHashType' => User::DEFAULT_PASSWORD_HASH,
                'enabled' => false,
            ]
        );
    }

    /**
     * Get externals updated field data values.
     *
     * This is a PHPUnit data provider
     *
     * @return array
     */
    public function assertUpdatedFieldDataLoadedCorrect(Field $field)
    {
        $this->assertInstanceOf(
            UserValue::class,
            $field->value
        );

        $expectedData = [
            'hasStoredLogin' => true,
            'login' => 'changeLogin',
            'email' => 'changeEmail@ez.no',
            'passwordHashType' => User::DEFAULT_PASSWORD_HASH,
            'enabled' => false,
        ];

        $this->assertPropertiesCorrect(
            $expectedData,
            $field->value
        );

        $this->assertNotNull($field->value->contentId);
    }

    /**
     * Get field data which will result in errors during update.
     *
     * This is a PHPUnit data provider.
     *
     * The returned records must contain of an error producing data value and
     * the expected exception class (from the API or SPI, not implementation
     * specific!) as the second element. For example:
     *
     * <code>
     * array(
     *      array(
     *          new DoomedValue( true ),
     *          'eZ\\Publish\\API\\Repository\\Exceptions\\ContentValidationException'
     *      ),
     *      // ...
     * );
     * </code>
     *
     * @return array[]
     */
    public function provideInvalidUpdateFieldData()
    {
        return [
            [
                null,
                NotNullConstraintViolationException::class,
            ],
            // @todo: Define more failure cases ...
        ];
    }

    /**
     * Asserts the the field data was loaded correctly.
     *
     * Asserts that the data provided by {@link getValidCreationFieldData()};
     * was copied and loaded correctly.
     *
     * @param Field $field
     */
    public function assertCopiedFieldDataLoadedCorrectly(Field $field)
    {
        $this->assertInstanceOf(
            UserValue::class,
            $field->value
        );

        $expectedData = [
            'hasStoredLogin' => false,
            'contentId' => null,
            'login' => null,
            'email' => null,
            'passwordHash' => null,
            'passwordHashType' => null,
            'enabled' => false,
            'maxLogin' => null,
        ];

        $this->assertPropertiesCorrect(
            $expectedData,
            $field->value
        );
    }

    /**
     * Get data to test to hash method.
     *
     * This is a PHPUnit data provider
     *
     * The returned records must have the the original value assigned to the
     * first index and the expected hash result to the second. For example:
     *
     * <code>
     * array(
     *      array(
     *          new MyValue( true ),
     *          array( 'myValue' => true ),
     *      ),
     *      // ...
     * );
     * </code>
     *
     * @return array
     */
    public function provideToHashData()
    {
        return [
            [
                new UserValue(['login' => self::TEST_LOGIN]),
                [
                    'login' => self::TEST_LOGIN,
                    'hasStoredLogin' => null,
                    'contentId' => null,
                    'email' => null,
                    'passwordHash' => null,
                    'passwordHashType' => null,
                    'enabled' => null,
                    'maxLogin' => null,
                    'plainPassword' => null,
                    'passwordUpdatedAt' => null,
                ],
            ],
        ];
    }

    /**
     * Get hashes and their respective converted values.
     *
     * This is a PHPUnit data provider
     *
     * The returned records must have the the input hash assigned to the
     * first index and the expected value result to the second. For example:
     *
     * <code>
     * array(
     *      array(
     *          array( 'myValue' => true ),
     *          new MyValue( true ),
     *      ),
     *      // ...
     * );
     * </code>
     *
     * @return array
     */
    public function provideFromHashData()
    {
        return [
            [
                ['login' => self::TEST_LOGIN],
                new UserValue(['login' => self::TEST_LOGIN]),
            ],
        ];
    }

    /**
     * Overwrite normal content creation.
     *
     * @param mixed $fieldData
     */
    protected function createContent($fieldData, $contentType = null)
    {
        if ($contentType === null) {
            $contentType = $this->testCreateContentType();
        }

        $repository = $this->getRepository();
        $userService = $repository->getUserService();

        // Instantiate a create struct with mandatory properties
        $userCreate = $userService->newUserCreateStruct(
            self::TEST_LOGIN,
            'hans@example.com',
            'PassWord42',
            'eng-US',
            $contentType
        );
        $userCreate->enabled = true;

        // Set some fields required by the user ContentType
        $userCreate->setField('name', 'Example User');

        // ID of the "Editors" user group in an eZ Publish demo installation
        $group = $userService->loadUserGroup(13);

        // Create a new user instance.
        $user = $userService->createUser($userCreate, [$group]);

        // Create draft from user content object
        $contentService = $repository->getContentService();

        return $contentService->createContentDraft($user->content->contentInfo, $user->content->versionInfo);
    }

    public function testCreateContentWithEmptyFieldValue()
    {
        $this->markTestSkipped('User field will never be created empty');
    }

    public function providerForTestIsEmptyValue()
    {
        return [
            [new UserValue()],
            [new UserValue([])],
        ];
    }

    public function providerForTestIsNotEmptyValue()
    {
        return [
            [
                $this->getValidCreationFieldData(),
            ],
        ];
    }

    public function testRemoveFieldDefinition()
    {
        $repository = $this->getRepository();
        $contentService = $repository->getContentService();
        $contentTypeService = $repository->getContentTypeService();
        $content = $this->testPublishContent();
        $countBeforeRemoval = count($content->getFields());

        $contentType = $contentTypeService->loadContentType($content->contentInfo->contentTypeId);
        $contentTypeDraft = $contentTypeService->createContentTypeDraft($contentType);

        $userFieldDefinition = $this->getUserFieldDefinition($contentType);

        $contentTypeService->removeFieldDefinition($contentTypeDraft, $userFieldDefinition);
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);

        $content = $contentService->loadContent($content->id);

        $this->assertCount($countBeforeRemoval - 1, $content->getFields());
        $this->assertNull($content->getFieldValue($userFieldDefinition->identifier));
    }

    public function testAddFieldDefinition()
    {
        // Field cannot be added to ContentType with existing content.
        $this->expectException(BadStateException::class);

        return parent::testAddFieldDefinition();
    }

    /**
     * @depends testCreateContent
     */
    public function testCopyField($content)
    {
        // Users cannot be copied.
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage(sprintf('User "%s" already exists', self::TEST_LOGIN));

        return parent::testCopyField($content);
    }

    /**
     * @depends testCopyField
     */
    public function testCopiedFieldType($content)
    {
        $this->markTestSkipped('Users cannot be copied, content is not passed to test.');
    }

    /**
     * @depends testCopiedFieldType
     */
    public function testCopiedExternalData(Field $field)
    {
        $this->markTestSkipped('Users cannot be copied, field is not passed to test.');
    }

    /**
     * @see https://jira.ez.no/browse/EZP-30966
     */
    public function testUpdateFieldDefinitionWithIncompleteSettingsSchema()
    {
        $contentTypeService = $this->getRepository()->getContentTypeService();
        $contentType = $this->testCreateContentType();
        $contentTypeDraft = $contentTypeService->createContentTypeDraft($contentType);

        $userFieldDefinition = $this->getUserFieldDefinition($contentType);
        $userFieldDefinitionUpdateStruct = $contentTypeService->newFieldDefinitionUpdateStruct();
        $userFieldDefinitionUpdateStruct->fieldSettings = [
            Type::PASSWORD_TTL_WARNING_SETTING => null,
            Type::REQUIRE_UNIQUE_EMAIL => false,
            Type::USERNAME_PATTERN => '.*',
        ];

        $contentTypeService->updateFieldDefinition($contentTypeDraft, $userFieldDefinition, $userFieldDefinitionUpdateStruct);
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);

        $contentType = $contentTypeService->loadContentType($contentType->id);
        $userFieldDefinition = $this->getUserFieldDefinition($contentType);

        $this->assertNull($userFieldDefinition->fieldSettings[Type::PASSWORD_TTL_WARNING_SETTING]);
    }

    /**
     * Finds ezuser field definition in given $contentType or mark test as failed if it doens't exists.
     *
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     *
     * @return \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition
     */
    private function getUserFieldDefinition(ContentType $contentType): FieldDefinition
    {
        $fieldDefinition = $contentType->getFirstFieldDefinitionOfType('ezuser');

        if ($fieldDefinition === null) {
            $this->fail("'ezuser' field definition was not found");
        }

        return $fieldDefinition;
    }
}
