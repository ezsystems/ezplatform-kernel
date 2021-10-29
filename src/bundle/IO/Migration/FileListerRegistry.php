<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\IO\Migration;

/**
 * A registry of FileListerInterfaces.
 */
interface FileListerRegistry
{
    /**
     * Returns the FileListerInterface matching the argument.
     *
     * @param string $identifier An identifier string.
     *
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException If no FileListerInterface exists with this identifier
     *
     * @return \Ibexa\Bundle\IO\Migration\FileListerInterface The FileListerInterface given by the identifier.
     */
    public function getItem($identifier);

    /**
     * Returns the identifiers of all registered FileListerInterfaces.
     *
     * @return string[] Array of identifier strings.
     */
    public function getIdentifiers();
}

class_alias(FileListerRegistry::class, 'eZ\Bundle\EzPublishIOBundle\Migration\FileListerRegistry');
