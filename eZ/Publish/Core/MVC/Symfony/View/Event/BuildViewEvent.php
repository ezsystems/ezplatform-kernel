<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\MVC\Symfony\View\Event;

use eZ\Publish\Core\MVC\Symfony\View\View;
use Symfony\Contracts\EventDispatcher\Event;

final class BuildViewEvent extends Event
{
    /** @var \eZ\Publish\Core\MVC\Symfony\View\View */
    private $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }

    public function getView(): View
    {
        return $this->view;
    }
}
