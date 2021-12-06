<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\View;

interface LocationValueView
{
    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location
     */
    public function getLocation();
}

class_alias(LocationValueView::class, 'eZ\Publish\Core\MVC\Symfony\View\LocationValueView');
