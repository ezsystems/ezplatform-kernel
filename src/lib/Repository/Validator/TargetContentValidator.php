<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\Validator;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\SPI\Persistence\Content;

/**
 * Validator for checking existence of content and its content type.
 *
 * @internal
 */
final class TargetContentValidator implements TargetContentValidatorInterface
{
    /** @var \eZ\Publish\SPI\Persistence\Content\Handler */
    private $contentHandler;

    /** @var \eZ\Publish\SPI\Persistence\Content\Type\Handler */
    private $contentTypeHandler;

    public function __construct(
        Content\Handler $contentHandler,
        Content\Type\Handler $contentTypeHandler
    ) {
        $this->contentHandler = $contentHandler;
        $this->contentTypeHandler = $contentTypeHandler;
    }

    public function validate(int $value, array $allowedContentTypes = []): ?ValidationError
    {
        try {
            $content = $this->contentHandler->load($value);
            $contentType = $this->contentTypeHandler->load($content->versionInfo->contentInfo->contentTypeId);

            if (!empty($allowedContentTypes) && !in_array($contentType->identifier, $allowedContentTypes, true)) {
                return new ValidationError(
                    'Content Type %contentTypeIdentifier% is not a valid relation target',
                    null,
                    [
                        '%contentTypeIdentifier%' => $contentType->identifier,
                    ],
                    'targetContentId'
                );
            }
        } catch (NotFoundException $e) {
            return new ValidationError(
                'Content with identifier %contentId% is not a valid relation target',
                null,
                [
                    '%contentId%' => $value,
                ],
                'targetContentId'
            );
        }

        return null;
    }
}
