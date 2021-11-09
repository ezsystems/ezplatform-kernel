<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\View\Provider;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;

/**
 * Interface for content view providers.
 *
 * Content view providers select a view for a given content, depending on its own internal rules.
 *
 * @deprecated since 6.0.0
 */
interface Content
{
    /**
     * Returns a ContentView object corresponding to $contentInfo, or null if not applicable.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo
     * @param string $viewType Variation of display for your content
     *
     * @return \Ibexa\Core\MVC\Symfony\View\ContentView|null
     */
    public function getView(ContentInfo $contentInfo, $viewType);
}

class_alias(Content::class, 'eZ\Publish\Core\MVC\Symfony\View\Provider\Content');
