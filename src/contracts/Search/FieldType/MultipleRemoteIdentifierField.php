<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Search\FieldType;

use Ibexa\Contracts\Core\Search\FieldType;

/**
 * Remote ID list document field.
 */
class MultipleRemoteIdentifierField extends FieldType
{
    /**
     * Search engine field type corresponding to remote ID list. The same MultipleIdentifierField due to BC.
     *
     * @see \Ibexa\Contracts\Core\Search\FieldType\MultipleIdentifierField
     *
     * @var string
     */
    protected $type = 'ez_mid';
}

class_alias(MultipleRemoteIdentifierField::class, 'eZ\Publish\SPI\Search\FieldType\MultipleRemoteIdentifierField');
