<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Security\PolicyProvider;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

class RepositoryPolicyProvider extends YamlPolicyProvider implements TranslationContainerInterface
{
    /**
     * @deprecated Deprecated since 7.1. No longer used. System policies configuration was moved to the eZ/Publish/Core/settings/policies.yml.
     */
    public function getFiles(): array
    {
        return [];
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message('role.policy.all_modules_all_functions', 'forms'))->setDesc('All modules / All functions'),
            (new Message('role.policy.class', 'forms'))->setDesc('Content Type'),
            (new Message('role.policy.class.all_functions', 'forms'))->setDesc('Content Type / All functions'),
            (new Message('role.policy.class.create', 'forms'))->setDesc('Content Type / Create'),
            (new Message('role.policy.class.delete', 'forms'))->setDesc('Content Type / Delete'),
            (new Message('role.policy.class.update', 'forms'))->setDesc('Content Type / Update'),
            (new Message('role.policy.content', 'forms'))->setDesc('Content'),
            (new Message('role.policy.content.all_functions', 'forms'))->setDesc('Content / All functions'),
            (new Message('role.policy.content.cleantrash', 'forms'))->setDesc('Content / Cleantrash'),
            (new Message('role.policy.content.create', 'forms'))->setDesc('Content / Create'),
            (new Message('role.policy.content.diff', 'forms'))->setDesc('Content / Diff'),
            (new Message('role.policy.content.edit', 'forms'))->setDesc('Content / Edit'),
            (new Message('role.policy.content.hide', 'forms'))->setDesc('Content / Hide'),
            (new Message('role.policy.content.manage_locations', 'forms'))->setDesc('Content / Manage locations'),
            (new Message('role.policy.content.pendinglist', 'forms'))->setDesc('Content / Pendinglist'),
            (new Message('role.policy.content.publish', 'forms'))->setDesc('Content / Publish'),
            (new Message('role.policy.content.read', 'forms'))->setDesc('Content / Read'),
            (new Message('role.policy.content.remove', 'forms'))->setDesc('Content / Remove'),
            (new Message('role.policy.content.restore', 'forms'))->setDesc('Content / Restore'),
            (new Message('role.policy.content.reverserelatedlist', 'forms'))->setDesc('Content / Reverserelatedlist'),
            (new Message('role.policy.content.translate', 'forms'))->setDesc('Content / Translate'),
            (new Message('role.policy.content.translations', 'forms'))->setDesc('Content / Translations'),
            (new Message('role.policy.content.urltranslator', 'forms'))->setDesc('Content / Urltranslator'),
            (new Message('role.policy.content.versionread', 'forms'))->setDesc('Content / Versionread'),
            (new Message('role.policy.content.versionremove', 'forms'))->setDesc('Content / Versionremove'),
            (new Message('role.policy.content.view_embed', 'forms'))->setDesc('Content / View embed'),
            (new Message('role.policy.role', 'forms'))->setDesc('Role'),
            (new Message('role.policy.role.all_functions', 'forms'))->setDesc('Role / All functions'),
            (new Message('role.policy.role.assign', 'forms'))->setDesc('Role / Assign'),
            (new Message('role.policy.role.create', 'forms'))->setDesc('Role / Create'),
            (new Message('role.policy.role.delete', 'forms'))->setDesc('Role / Delete'),
            (new Message('role.policy.role.read', 'forms'))->setDesc('Role / Read'),
            (new Message('role.policy.role.update', 'forms'))->setDesc('Role / Update'),
            (new Message('role.policy.section', 'forms'))->setDesc('Section'),
            (new Message('role.policy.section.all_functions', 'forms'))->setDesc('Section / All functions'),
            (new Message('role.policy.section.assign', 'forms'))->setDesc('Section / Assign'),
            (new Message('role.policy.section.edit', 'forms'))->setDesc('Section / Edit'),
            (new Message('role.policy.section.view', 'forms'))->setDesc('Section / View'),
            (new Message('role.policy.setting', 'forms'))->setDesc('Setting'),
            (new Message('role.policy.setting.all_functions', 'forms'))->setDesc('Setting / All functions'),
            (new Message('role.policy.setting.create', 'forms'))->setDesc('Setting / Create'),
            (new Message('role.policy.setting.remove', 'forms'))->setDesc('Setting / Remove'),
            (new Message('role.policy.setting.update', 'forms'))->setDesc('Setting / Update'),
            (new Message('role.policy.setup', 'forms'))->setDesc('Setup'),
            (new Message('role.policy.setup.administrate', 'forms'))->setDesc('Setup / Administrate'),
            (new Message('role.policy.setup.all_functions', 'forms'))->setDesc('Setup / All functions'),
            (new Message('role.policy.setup.install', 'forms'))->setDesc('Setup / Install'),
            (new Message('role.policy.setup.setup', 'forms'))->setDesc('Setup / Setup'),
            (new Message('role.policy.setup.system_info', 'forms'))->setDesc('Setup / System info'),
            (new Message('role.policy.state', 'forms'))->setDesc('State'),
            (new Message('role.policy.state.administrate', 'forms'))->setDesc('State / Administrate'),
            (new Message('role.policy.state.all_functions', 'forms'))->setDesc('State / All functions'),
            (new Message('role.policy.state.assign', 'forms'))->setDesc('State / Assign'),
            (new Message('role.policy.url', 'forms'))->setDesc('Url'),
            (new Message('role.policy.url.all_functions', 'forms'))->setDesc('Url / All functions'),
            (new Message('role.policy.url.update', 'forms'))->setDesc('Url / Update'),
            (new Message('role.policy.url.view', 'forms'))->setDesc('Url / View'),
            (new Message('role.policy.user', 'forms'))->setDesc('User'),
            (new Message('role.policy.user.activation', 'forms'))->setDesc('User / Activation'),
            (new Message('role.policy.user.all_functions', 'forms'))->setDesc('User / All functions'),
            (new Message('role.policy.user.login', 'forms'))->setDesc('User / Login'),
            (new Message('role.policy.user.password', 'forms'))->setDesc('User / Password'),
            (new Message('role.policy.user.preferences', 'forms'))->setDesc('User / Preferences'),
            (new Message('role.policy.user.register', 'forms'))->setDesc('User / Register'),
            (new Message('role.policy.user.selfedit', 'forms'))->setDesc('User / Selfedit'),
        ];
    }
}
