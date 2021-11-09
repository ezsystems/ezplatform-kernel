<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\EventListener\Stubs;

use Symfony\Component\Console\Output\Output;

/**
 * Stub class for TestOutput Output.
 */
class TestOutput extends Output
{
    public $output = '';

    public function clear()
    {
        $this->output = '';
    }

    protected function doWrite($message, $newline)
    {
        $this->output .= $message . ($newline ? "\n" : '');
    }
}

class_alias(TestOutput::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\EventListener\Stubs\TestOutput');
