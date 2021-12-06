<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\Converter;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;

class ContentParamConverter extends RepositoryParamConverter
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    protected function getSupportedClass()
    {
        return Content::class;
    }

    protected function getPropertyName()
    {
        return 'contentId';
    }

    protected function loadValueObject($id)
    {
        return $this->contentService->loadContent($id);
    }
}

class_alias(ContentParamConverter::class, 'eZ\Bundle\EzPublishCoreBundle\Converter\ContentParamConverter');
