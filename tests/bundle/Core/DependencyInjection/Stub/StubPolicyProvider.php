<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\DependencyInjection\Stub;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigBuilderInterface;
use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface;

/**
 * For tests only!!!
 * Dummy policy provider that does return policies it's given in constructor.
 */
class StubPolicyProvider implements PolicyProviderInterface
{
    /** @var array */
    private $policies;

    public function __construct(array $policies)
    {
        $this->policies = $policies;
    }

    public function addPolicies(ConfigBuilderInterface $configBuilder)
    {
        $configBuilder->addConfig($this->policies);
    }
}

class_alias(StubPolicyProvider::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Stub\StubPolicyProvider');
