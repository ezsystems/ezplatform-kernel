<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\FieldType\Relation;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\MVC\Symfony\FieldType\View\ParameterProviderInterface;

class ParameterProvider implements ParameterProviderInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /**
     * @param \Ibexa\Contracts\Core\Repository\ContentService $contentService
     */
    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * Returns a hash of parameters to inject to the associated fieldtype's view template.
     * Returned parameters will only be available for associated field type.
     *
     * Key is the parameter name (the variable name exposed in the template, in the 'parameters' array).
     * Value is the parameter's value.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Field $field The field parameters are provided for.
     *
     * @return array
     */
    public function getViewParameters(Field $field): array
    {
        try {
            $contentInfo = null;
            if ($field->value->destinationContentId !== null) {
                $contentInfo = $this->contentService->loadContentInfo(
                    $field->value->destinationContentId
                );
            }

            return [
                'available' => $contentInfo !== null && !$contentInfo->isTrashed(),
            ];
        } catch (NotFoundException | UnauthorizedException $exception) {
            return [
                'available' => false,
            ];
        }
    }
}

class_alias(ParameterProvider::class, 'eZ\Publish\Core\MVC\Symfony\FieldType\Relation\ParameterProvider');
