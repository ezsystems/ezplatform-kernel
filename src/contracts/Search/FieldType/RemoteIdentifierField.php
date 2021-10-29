<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Search\FieldType;

use Ibexa\Contracts\Core\Search\FieldType;

/**
 * Remote ID document field.
 */
final class RemoteIdentifierField extends FieldType
{
    /**
     * Search engine field type corresponding to remote ID. The same as IdentifierField due to BC.
     *
     * @see \Ibexa\Contracts\Core\Search\FieldType\IdentifierField
     *
     * @var string
     */
    protected $type = 'ez_id';
}

class_alias(RemoteIdentifierField::class, 'eZ\Publish\SPI\Search\FieldType\RemoteIdentifierField');
