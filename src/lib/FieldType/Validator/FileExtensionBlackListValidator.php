<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\Validator;

use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Core\FieldType\Validator;
use Ibexa\Core\FieldType\Value as BaseValue;
use Ibexa\Core\MVC\ConfigResolverInterface;

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
     * @param \Ibexa\Core\MVC\ConfigResolverInterface $configResolver
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
        if (
            pathinfo($value->fileName, PATHINFO_BASENAME) !== $value->fileName ||
            in_array(strtolower(pathinfo($value->fileName, PATHINFO_EXTENSION)), $this->constraints['extensionsBlackList'], true)
        ) {
            $this->errors[] = new ValidationError(
                'A valid file is required. Following file extensions are on the blacklist: %extensionsBlackList%',
                null,
                [
                    '%extensionsBlackList%' => implode(', ', $this->constraints['extensionsBlackList']),
                ],
                'fileExtensionBlackList'
            );

            return false;
        }

        return true;
    }
}

class_alias(FileExtensionBlackListValidator::class, 'eZ\Publish\Core\FieldType\Validator\FileExtensionBlackListValidator');
