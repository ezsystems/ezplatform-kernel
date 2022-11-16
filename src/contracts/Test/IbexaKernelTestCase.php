<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Test;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @experimental
 */
abstract class IbexaKernelTestCase extends KernelTestCase
{
    use IbexaKernelTestTrait;

    protected static function getKernelClass(): string
    {
        try {
            return parent::getKernelClass();
        } catch (LogicException $e) {
            return IbexaTestKernel::class;
        }
    }
}
