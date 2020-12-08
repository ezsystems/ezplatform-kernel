<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\PlatformInstallerBundle\Event;

use Symfony\Component\Console\Command\Command;
use Symfony\Contracts\EventDispatcher\Event;

final class InstallerCommandConfigureEvent extends Event
{
    /** @var \Symfony\Component\Console\Command\Command */
    private $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    public function getCommand(): Command
    {
        return $this->command;
    }
}
