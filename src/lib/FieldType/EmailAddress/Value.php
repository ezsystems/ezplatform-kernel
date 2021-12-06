<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\EmailAddress;

use Ibexa\Core\FieldType\Value as BaseValue;

/**
 * Value for EMailAddress field type.
 */
class Value extends BaseValue
{
    /**
     * Email address.
     *
     * @var string
     */
    public $email;

    /**
     * Construct a new Value object and initialize its $email.
     *
     * @param string $email
     */
    public function __construct($email = '')
    {
        $this->email = $email;
    }

    public function __toString()
    {
        return (string)$this->email;
    }
}

class_alias(Value::class, 'eZ\Publish\Core\FieldType\EmailAddress\Value');
