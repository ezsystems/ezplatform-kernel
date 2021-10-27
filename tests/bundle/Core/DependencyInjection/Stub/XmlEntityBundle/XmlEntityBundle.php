<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Core\DependencyInjection\Stub\XmlEntityBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class XmlEntityBundle extends Bundle
{
}

class_alias(XmlEntityBundle::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Stub\XmlEntityBundle\XmlEntityBundle');
