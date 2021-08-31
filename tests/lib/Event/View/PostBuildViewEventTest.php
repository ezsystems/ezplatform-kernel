<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Event\View;

use eZ\Publish\Core\MVC\Symfony\View\BaseView;
use Ibexa\Contracts\Core\Event\View\PostBuildViewEvent;
use PHPUnit\Framework\TestCase;

class PostBuildViewEventTest extends TestCase
{
    public function testEventConstruction(): void
    {
        $view = new class() extends BaseView {
        };

        $event = new PostBuildViewEvent($view);

        self::assertSame($view, $event->getView());
    }
}
