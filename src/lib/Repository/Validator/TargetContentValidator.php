<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\Validator;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\FieldType\ValidationError;

/**
 * Validator for checking existence of content and its content type.
 *
 * @internal
 */
final class TargetContentValidator implements TargetContentValidatorInterface
{
    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function validate(int $value, array $allowedContentTypes = []): ?ValidationError
    {
        try {
            $contentInfo = $this->repository->sudo(static function (Repository $repository) use ($value) {
                return $repository->getContentService()->loadContentInfo($value);
            });
            $contentType = $this->repository->getContentTypeService()->loadContentType($contentInfo->contentTypeId);

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
                'Content is not a valid relation target',
                null,
                [],
                'targetContentId'
            );
        }

        return null;
    }
}
