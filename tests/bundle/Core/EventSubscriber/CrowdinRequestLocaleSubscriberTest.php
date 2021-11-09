<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\EventSubscriber;

use Ibexa\Bundle\Core\EventSubscriber\CrowdinRequestLocaleSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class CrowdinRequestLocaleSubscriberTest extends TestCase
{
    /**
     * @dataProvider testSetRequestsProvider
     */
    public function testSetLocale(Request $request, $shouldHaveCustomLocale)
    {
        $event = new RequestEvent(
            $this->getMockBuilder(HttpKernelInterface::class)->getMock(),
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $subscriber = new CrowdinRequestLocaleSubscriber();
        $subscriber->setInContextAcceptLanguage($event);

        $this->assertEquals(
            $shouldHaveCustomLocale,
            'ach_UG' === $event->getRequest()->getPreferredLanguage(),
            'The custom ach_UG locale was expected to be set by the event subscriber'
        );
    }

    public function testSetRequestsProvider()
    {
        return [
            'with_ez_in_context_translation_cookie' => [
                new Request([], [], [], ['ez_in_context_translation' => '1']),
                true,
            ],
            'without_ez_in_context_translation_cookie' => [
                new Request([], [], [], []),
                false,
            ],
        ];
    }
}

class_alias(CrowdinRequestLocaleSubscriberTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\EventSubscriber\CrowdinRequestLocaleSubscriberTest');
