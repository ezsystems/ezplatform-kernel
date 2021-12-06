<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Decorator;

use Ibexa\Contracts\Core\Repository\FieldType;
use Ibexa\Contracts\Core\Repository\FieldTypeService;

abstract class FieldTypeServiceDecorator implements FieldTypeService
{
    /** @var \Ibexa\Contracts\Core\Repository\FieldTypeService */
    protected $innerService;

    public function __construct(FieldTypeService $innerService)
    {
        $this->innerService = $innerService;
    }

    public function getFieldTypes(): iterable
    {
        return $this->innerService->getFieldTypes();
    }

    public function getFieldType(string $identifier): FieldType
    {
        return $this->innerService->getFieldType($identifier);
    }

    public function hasFieldType(string $identifier): bool
    {
        return $this->innerService->hasFieldType($identifier);
    }
}

class_alias(FieldTypeServiceDecorator::class, 'eZ\Publish\SPI\Repository\Decorator\FieldTypeServiceDecorator');
