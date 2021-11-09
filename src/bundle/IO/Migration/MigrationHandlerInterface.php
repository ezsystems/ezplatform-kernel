<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\IO\Migration;

interface MigrationHandlerInterface
{
    /**
     * Set the from/to handlers based on identifiers.
     * Returns the MigrationHandler.
     *
     * @param string $fromMetadataHandlerIdentifier
     * @param string $fromBinarydataHandlerIdentifier
     * @param string $toMetadataHandlerIdentifier
     * @param string $toBinarydataHandlerIdentifier
     *
     * @return MigrationHandlerInterface
     */
    public function setIODataHandlersByIdentifiers(
        $fromMetadataHandlerIdentifier,
        $fromBinarydataHandlerIdentifier,
        $toMetadataHandlerIdentifier,
        $toBinarydataHandlerIdentifier
    );
}

class_alias(MigrationHandlerInterface::class, 'eZ\Bundle\EzPublishIOBundle\Migration\MigrationHandlerInterface');
