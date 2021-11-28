<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\IO\Migration;

use Ibexa\Contracts\Core\IO\BinaryFile;

/**
 * Interface for file migrators, mandates the migrateFile method.
 */
interface FileMigratorInterface extends MigrationHandlerInterface
{
    /**
     * Migrate a file.
     *
     * @param \Ibexa\Contracts\Core\IO\BinaryFile $binaryFile Information about the file
     *
     * @return bool Success or failure
     */
    public function migrateFile(BinaryFile $binaryFile);
}

class_alias(FileMigratorInterface::class, 'eZ\Bundle\EzPublishIOBundle\Migration\FileMigratorInterface');
