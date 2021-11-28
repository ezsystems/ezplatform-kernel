<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\View;

/**
 * A view that can embedded into another element.
 */
interface EmbedView
{
    /**
     * The default viewType for embed views.
     *
     * @var string
     */
    public const DEFAULT_VIEW_TYPE = 'embed';

    /**
     * Sets the value as embed / not embed.
     *
     * @param bool $value
     */
    public function setIsEmbed($value);

    /**
     * Is the view an embed or not.
     *
     * @return bool True if the view is an embed, false if it is not.
     */
    public function isEmbed();
}

class_alias(EmbedView::class, 'eZ\Publish\Core\MVC\Symfony\View\EmbedView');
