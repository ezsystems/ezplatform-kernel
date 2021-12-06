<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\EventListener;

use Ibexa\Bundle\Core\EventListener\ConsoleCommandListener;
use Ibexa\Core\MVC\Exception\InvalidSiteAccessException;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Tests\Bundle\Core\EventListener\Stubs\TestOutput;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ConsoleCommandListenerTest extends TestCase
{
    private const INVALID_SA_NAME = 'foo';

    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess */
    private $siteAccess;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $dispatcher;

    /** @var \Ibexa\Bundle\Core\EventListener\ConsoleCommandListener */
    private $listener;

    /** @var \Symfony\Component\Console\Input\InputDefinition */
    private $inputDefinition;

    /** @var \Symfony\Component\Console\Output\Output */
    private $testOutput;

    /** @var \Symfony\Component\Console\Command\Command|\PHPUnit\Framework\MockObject\MockObject */
    private $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->siteAccess = new SiteAccess('test');
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->listener = new ConsoleCommandListener('default', $this->getSiteAccessProviderMock(), $this->dispatcher);
        $this->listener->setSiteAccess($this->siteAccess);
        $this->dispatcher->addSubscriber($this->listener);
        $this->inputDefinition = new InputDefinition([new InputOption('siteaccess', null, InputOption::VALUE_OPTIONAL)]);
        $this->testOutput = new TestOutput(Output::VERBOSITY_QUIET, true);
        $this->command = $this->createMock(Command::class);
    }

    public function testGetSubscribedEvents()
    {
        $this->assertSame(
            [
                ConsoleEvents::COMMAND => [['onConsoleCommand', 128]],
            ],
            $this->listener->getSubscribedEvents()
        );
    }

    public function testInvalidSiteAccessDev()
    {
        $this->expectException(InvalidSiteAccessException::class);
        $this->expectExceptionMessageMatches('/^Invalid SiteAccess \'foo\', matched by .+\\. Valid SiteAccesses are/');

        $this->dispatcher->expects($this->never())
            ->method('dispatch');
        $input = new ArrayInput(['--siteaccess' => 'foo'], $this->inputDefinition);
        $event = new ConsoleCommandEvent($this->command, $input, $this->testOutput);
        $this->listener->setDebug(true);
        $this->listener->onConsoleCommand($event);
    }

    public function testInvalidSiteAccessProd()
    {
        $this->expectException(InvalidSiteAccessException::class);
        $this->expectExceptionMessageMatches('/^Invalid SiteAccess \'foo\', matched by .+\\.$/');

        $this->dispatcher->expects($this->never())
            ->method('dispatch');
        $input = new ArrayInput(['--siteaccess' => 'foo'], $this->inputDefinition);
        $event = new ConsoleCommandEvent($this->command, $input, $this->testOutput);
        $this->listener->setDebug(false);
        $this->listener->onConsoleCommand($event);
    }

    public function testValidSiteAccess()
    {
        $this->dispatcher->expects($this->once())
            ->method('dispatch');
        $input = new ArrayInput(['--siteaccess' => 'site1'], $this->inputDefinition);
        $event = new ConsoleCommandEvent($this->command, $input, $this->testOutput);
        $this->listener->onConsoleCommand($event);
        $this->assertEquals(new SiteAccess('site1', 'cli'), $this->siteAccess);
    }

    public function testDefaultSiteAccess()
    {
        $this->dispatcher->expects($this->once())
            ->method('dispatch');
        $input = new ArrayInput([], $this->inputDefinition);
        $event = new ConsoleCommandEvent($this->command, $input, $this->testOutput);
        $this->listener->onConsoleCommand($event);
        $this->assertEquals(new SiteAccess('default', 'cli'), $this->siteAccess);
    }

    private function getSiteAccessProviderMock(): SiteAccess\SiteAccessProviderInterface
    {
        $siteAccessProviderMock = $this->createMock(SiteAccess\SiteAccessProviderInterface::class);
        $siteAccessProviderMock
            ->method('isDefined')
            ->willReturnMap([
                ['default', true],
                ['site1', true],
                [self::INVALID_SA_NAME, false],
            ]);

        return $siteAccessProviderMock;
    }
}

class_alias(ConsoleCommandListenerTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\EventListener\ConsoleCommandListenerTest');
