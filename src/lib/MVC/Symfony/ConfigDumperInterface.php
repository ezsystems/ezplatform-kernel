<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony;

/**
 * Interface for configuration dumpers.
 * Use this interface when you want to dump settings in a configuration file for instance.
 */
interface ConfigDumperInterface
{
    public const OPT_DEFAULT = 0;
    public const OPT_BACKUP_CONFIG = 1;

    /**
     * Dumps settings contained in $configArray in a configuration storage (e.g. a YAML config file).
     *
     * @param array $configArray Hash of settings.
     * @param int $options A binary combination of options. See class OPT_* class constants in {@link \Ibexa\Core\MVC\Symfony\ConfigDumperInterface}
     */
    public function dump(array $configArray, $options = self::OPT_DEFAULT);
}

class_alias(ConfigDumperInterface::class, 'eZ\Publish\Core\MVC\Symfony\ConfigDumperInterface');
