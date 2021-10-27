<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider;

/**
 * @deprecated Deprecated since 7.1. No longer used. System policies configuration was moved to src/lib/Resources/settings/policies.yml.
 */
class RepositoryPolicyProvider extends YamlPolicyProvider
{
    public function getFiles()
    {
        return [];
    }
}

class_alias(RepositoryPolicyProvider::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Security\PolicyProvider\RepositoryPolicyProvider');
