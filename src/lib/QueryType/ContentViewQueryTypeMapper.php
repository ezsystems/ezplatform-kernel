<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\QueryType;

use Ibexa\Core\MVC\Symfony\View\ContentView;

/**
 * Maps a ContentView to a QueryType.
 */
interface ContentViewQueryTypeMapper
{
    /**
     * @param \Ibexa\Core\MVC\Symfony\View\ContentView $contentView
     *
     * @return \Ibexa\Core\QueryType\QueryType
     */
    public function map(ContentView $contentView);
}

class_alias(ContentViewQueryTypeMapper::class, 'eZ\Publish\Core\QueryType\ContentViewQueryTypeMapper');
