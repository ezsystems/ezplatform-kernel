<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Bundle\EzPublishCoreBundle\Tests\EventListener;

use eZ\Bundle\EzPublishCoreBundle\EventListener\RejectExplicitFrontControllerRequestsListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use PHPUnit\Framework\TestCase;

class RejectExplicitFrontControllerRequestsListenerTest extends TestCase
{
    /** @var \eZ\Bundle\EzPublishCoreBundle\EventListener\RejectExplicitFrontControllerRequestsListener */
    private $eventListener;

    /** @var \Symfony\Component\HttpKernel\HttpKernelInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $httpKernel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->eventListener = new RejectExplicitFrontControllerRequestsListener();
        $this->httpKernel = $this->createMock(HttpKernelInterface::class);
    }

    public function testSubscribedEvents(): void
    {
        $this->assertSame(
            [
                KernelEvents::REQUEST => [
                    ['onKernelRequest', 255],
                ],
            ],
            RejectExplicitFrontControllerRequestsListener::getSubscribedEvents()
        );
    }

    /**
     * @dataProvider validRequestDataProvider
     * @doesNotPerformAssertions
     */
    public function testOnKernelRequest(Request $request): void
    {
        $event = new RequestEvent(
            $this->httpKernel,
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $this->eventListener->onKernelRequest($event);
    }

    /**
     * @dataProvider prohibitedRequestDataProvider
     */
    public function testOnKernelRequestThrowsException(Request $request): void
    {
        $this->expectException(NotFoundHttpException::class);

        $event = new RequestEvent(
            $this->httpKernel,
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $this->eventListener->onKernelRequest($event);
    }

    public function validRequestDataProvider(): array
    {
        return [
            [
                Request::create(
                    'https://example.com',
                    'GET',
                    [],
                    [],
                    [],
                    [
                        'REQUEST_URI' => 'https://example.com/app.php',
                        'SCRIPT_FILENAME' => 'app.php',
                    ]
                ),
            ],
            [
                Request::create(
                    'https://example.com/',
                    'GET',
                    [],
                    [],
                    [],
                    [
                        'REQUEST_URI' => 'https://example.com/app.php/',
                        'SCRIPT_FILENAME' => 'app.php',
                    ]
                ),
            ],
            [
                Request::create(
                    'https://example.com/admin/dashboard',
                    'GET',
                    [],
                    [],
                    [],
                    [
                        'REQUEST_URI' => 'https://example.com/app.php/admin/dashboard',
                        'SCRIPT_FILENAME' => 'app.php',
                    ]
                ),
            ],
            [
                Request::create(
                    'https://example.com/admin/dashboard/',
                    'GET',
                    [],
                    [],
                    [],
                    [
                        'REQUEST_URI' => 'https://example.com/app.php/admin/dashboard/',
                        'SCRIPT_FILENAME' => 'app.php',
                    ]
                ),
            ],
            [
                Request::create(
                    'https://example.com/Folder/Content',
                    'GET',
                    [],
                    [],
                    [],
                    [
                        'REQUEST_URI' => 'https://example.com/app.php/Folder/Content',
                        'SCRIPT_FILENAME' => 'app.php',
                    ]
                ),
            ],
            [
                Request::create(
                    'https://example.com/Folder/Content/',
                    'GET',
                    [],
                    [],
                    [],
                    [
                        'REQUEST_URI' => 'https://example.com/app.php/Folder/Content/',
                        'SCRIPT_FILENAME' => 'app.php',
                    ]
                ),
            ],
            [
                Request::create(
                    'https://example.com/app.php-foo',
                    'GET',
                    [],
                    [],
                    [],
                    [
                        'REQUEST_URI' => 'https://example.com/app.php/app.php-foo',
                        'SCRIPT_FILENAME' => 'app.php',
                    ]
                ),
            ],
            [
                Request::create(
                    'https://example.com/app.php.foo',
                    'GET',
                    [],
                    [],
                    [],
                    [
                        'REQUEST_URI' => 'https://example.com/app.php.foo',
                        'SCRIPT_FILENAME' => 'app.php',
                    ]
                ),
            ],
            [
                Request::create(
                    'https://example.com/folder/folder/app.php',
                    'GET',
                    [],
                    [],
                    [],
                    [
                        'REQUEST_URI' => 'https://example.com/app.php/folder/folder/app.php',
                        'SCRIPT_FILENAME' => 'app.php',
                    ]
                ),
            ],
        ];
    }

    public function prohibitedRequestDataProvider(): array
    {
        return [
            [
                Request::create(
                    'https://example.com/app.php',
                    'GET',
                    [],
                    [],
                    [],
                    [
                        'REQUEST_URI' => 'https://example.com/app.php',
                        'SCRIPT_FILENAME' => 'app.php',
                    ]
                ),
            ],
            [
                Request::create(
                    'https://example.com/app.php/app.php',
                    'GET',
                    [],
                    [],
                    [],
                    [
                        'REQUEST_URI' => 'https://example.com/app.php/app.php',
                        'SCRIPT_FILENAME' => 'app.php',
                    ]
                ),
            ],
            [
                Request::create(
                    'https://example.com/folder/app.php',
                    'GET',
                    [],
                    [],
                    [],
                    [
                        'REQUEST_URI' => 'https://example.com/app.php/folder/app.php',
                        'SCRIPT_FILENAME' => 'app.php',
                    ]
                ),
            ],
            [
                Request::create(
                    'https://example.com/app.php/foo',
                    'GET',
                    [],
                    [],
                    [],
                    [
                        'REQUEST_URI' => 'https://example.com/app.php/app.php/foo',
                        'SCRIPT_FILENAME' => 'app.php',
                    ]
                ),
            ],
            [
                Request::create(
                    'https://example.com/app.php?foo=bar',
                    'GET',
                    [],
                    [],
                    [],
                    [
                        'REQUEST_URI' => 'https://example.com/app.php/app.php?foo=bar',
                        'SCRIPT_FILENAME' => 'app.php',
                    ]
                ),
            ],
            [
                Request::create(
                    'https://example.com/app.php#foo',
                    'GET',
                    [],
                    [],
                    [],
                    [
                        'REQUEST_URI' => 'https://example.com/app.php/app.php#foo',
                        'SCRIPT_FILENAME' => 'app.php',
                    ]
                ),
            ],
        ];
    }
}
