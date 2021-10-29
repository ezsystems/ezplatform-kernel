<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\MVC\Symfony\Templating\Twig;

interface ResourceProviderInterface
{
    /**
     * @return array|\Twig\Template[]
     */
    public function getFieldViewResources(): array;

    /**
     * @return array|\Twig\Template[]
     */
    public function getFieldEditResources(): array;

    /**
     * @return array|\Twig\Template[]
     */
    public function getFieldDefinitionViewResources(): array;

    /**
     * @return array|\Twig\Template[]
     */
    public function getFieldDefinitionEditResources(): array;
}

class_alias(ResourceProviderInterface::class, 'eZ\Publish\Core\MVC\Symfony\Templating\Twig\ResourceProviderInterface');
