<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Core\DependencyInjection\Stub\AnnotationEntityBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AnnotationEntityBundle extends Bundle
{
}

class_alias(AnnotationEntityBundle::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Stub\AnnotationEntityBundle\AnnotationEntityBundle');
