<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\URLChecker;

use Ibexa\Bundle\Core\URLChecker\URLChecker;
use Ibexa\Bundle\Core\URLChecker\URLHandlerInterface;
use Ibexa\Bundle\Core\URLChecker\URLHandlerRegistryInterface;
use Ibexa\Contracts\Core\Repository\URLService;
use Ibexa\Contracts\Core\Repository\Values\URL\SearchResult;
use Ibexa\Contracts\Core\Repository\Values\URL\URL;
use Ibexa\Contracts\Core\Repository\Values\URL\URLQuery;
use Ibexa\Contracts\Core\Repository\Values\URL\URLUpdateStruct;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class URLCheckerTest extends TestCase
{
    /** @var \Ibexa\Contracts\Core\Repository\URLService|\PHPUnit\Framework\MockObject\MockObject */
    private $urlService;

    /** @var \Ibexa\Bundle\Core\URLChecker\URLHandlerRegistryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $handlerRegistry;

    /** @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $logger;

    protected function setUp(): void
    {
        $this->urlService = $this->createMock(URLService::class);
        $this->urlService
            ->expects($this->any())
            ->method('createUpdateStruct')
            ->willReturnCallback(static function () {
                return new URLUpdateStruct();
            });

        $this->handlerRegistry = $this->createMock(URLHandlerRegistryInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    public function testCheck()
    {
        $query = new URLQuery();
        $groups = $this->createGroupedUrls(['http', 'https']);

        $this->urlService
            ->expects($this->once())
            ->method('findUrls')
            ->with($query)
            ->willReturn($this->createSearchResults($groups));

        $handlers = [
            'http' => $this->createMock(URLHandlerInterface::class),
            'https' => $this->createMock(URLHandlerInterface::class),
        ];

        foreach ($handlers as $scheme => $handler) {
            $handler
                ->expects($this->once())
                ->method('validate')
                ->willReturnCallback(function (array $urls) use ($scheme, $groups) {
                    $this->assertEqualsCanonicalizing($groups[$scheme], $urls);
                });
        }

        $this->configureUrlHandlerRegistry($handlers);

        $urlChecker = $this->createUrlChecker();
        $urlChecker->check($query);
    }

    public function testCheckUnsupported()
    {
        $query = new URLQuery();
        $groups = $this->createGroupedUrls(['http', 'https'], 10);

        $this->urlService
            ->expects($this->once())
            ->method('findUrls')
            ->with($query)
            ->willReturn($this->createSearchResults($groups));

        $this->logger
            ->expects($this->atLeastOnce())
            ->method('error')
            ->with('Unsupported URL schema: https');

        $handlers = [
            'http' => $this->createMock(URLHandlerInterface::class),
        ];

        foreach ($handlers as $scheme => $handler) {
            $handler
                ->expects($this->once())
                ->method('validate')
                ->willReturnCallback(function (array $urls) use ($scheme, $groups) {
                    $this->assertEqualsCanonicalizing($groups[$scheme], $urls);
                });
        }

        $this->configureUrlHandlerRegistry($handlers);

        $urlChecker = $this->createUrlChecker();
        $urlChecker->check($query);
    }

    private function configureUrlHandlerRegistry(array $schemes)
    {
        $this->handlerRegistry
            ->method('supported')
            ->willReturnCallback(static function ($scheme) use ($schemes) {
                return isset($schemes[$scheme]);
            });

        $this->handlerRegistry
            ->method('getHandler')
            ->willReturnCallback(static function ($scheme) use ($schemes) {
                return $schemes[$scheme];
            });
    }

    private function createSearchResults(array &$urls)
    {
        $input = array_reduce($urls, 'array_merge', []);

        shuffle($input);

        return new SearchResult([
            'totalCount' => count($input),
            'items' => $input,
        ]);
    }

    private function createGroupedUrls(array $schemes, $n = 10)
    {
        $results = [];

        foreach ($schemes as $i => $scheme) {
            $results[$scheme] = [];
            for ($j = 0; $j < $n; ++$j) {
                $results[$scheme][] = new URL([
                    'id' => $i * 100 + $j,
                    'url' => $scheme . '://' . $j,
                ]);
            }
        }

        return $results;
    }

    /**
     * @return \Ibexa\Bundle\Core\URLChecker\URLChecker
     */
    private function createUrlChecker()
    {
        $urlChecker = new URLChecker(
            $this->urlService,
            $this->handlerRegistry
        );
        $urlChecker->setLogger($this->logger);

        return $urlChecker;
    }
}

class_alias(URLCheckerTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\URLChecker\URLCheckerTest');
