<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\FieldType\Validator;

use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\Core\FieldType\Validator;
use eZ\Publish\Core\FieldType\Value as BaseValue;
use eZ\Publish\Core\MVC\ConfigResolverInterface;

class FileExtensionBlackListValidator extends Validator
{
    protected $constraints = [
        'extensionsBlackList' => [],
    ];

    protected $constraintsSchema = [
        'extensionsBlackList' => [
            'type' => 'array',
            'default' => [],
        ],
    ];

    /**
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     */
    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->constraints['extensionsBlackList'] = $configResolver->getParameter(
            'io.file_storage.file_type_blacklist'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function validateConstraints($constraints)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function validate(BaseValue $value)
    {
        $this->errors = [];

        $this->validateFileExtension($value->fileName);

        return empty($this->errors);
    }

    public function validateFileExtension(string $fileName): void
    {
        if (
            pathinfo($fileName, PATHINFO_BASENAME) !== $fileName
            || in_array(
                strtolower(pathinfo($fileName, PATHINFO_EXTENSION)),
                $this->constraints['extensionsBlackList'],
                true
            )
        ) {
            $this->errors[] = new ValidationError(
                'A valid file is required. The following file extensions are not allowed: %extensionsBlackList%',
                null,
                [
                    '%extensionsBlackList%' => implode(', ', $this->constraints['extensionsBlackList']),
                ],
                'fileExtensionBlackList'
            );
        }
    }

    /**
     * @return array<\eZ\Publish\SPI\FieldType\ValidationError>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
