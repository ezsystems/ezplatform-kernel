<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\PlatformInstallerBundle\Event;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Output\Output;
use Symfony\Contracts\EventDispatcher\Event;

final class InstallerCommandExecuteEvent extends Event
{
    /** @var \Symfony\Component\Console\Input\Input */
    private $input;

    /** @var \Symfony\Component\Console\Output\Output */
    private $output;

    public function __construct(
        Input $input,
        Output $output
    ) {
        $this->input = $input;
        $this->output = $output;
    }

    public function getInput(): Input
    {
        return $this->input;
    }

    public function getOutput(): Output
    {
        return $this->output;
    }
}
