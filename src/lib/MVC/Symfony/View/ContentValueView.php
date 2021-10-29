<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\View;

/**
 * A view that contains a Content.
 */
interface ContentValueView
{
    /**
     * Returns the Content.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Content
     */
    public function getContent();
}

class_alias(ContentValueView::class, 'eZ\Publish\Core\MVC\Symfony\View\ContentValueView');
