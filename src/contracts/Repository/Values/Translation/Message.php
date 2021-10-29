<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Repository\Values\Translation;

use Ibexa\Contracts\Core\Repository\Values\Translation;

/**
 * Class for translatable messages, which only occur in singular form.
 *
 * The message might include replacements, in the form %[A-Za-z]%. Those are
 * replaced by the values provided. A raw % can be escaped like %%.
 */
class Message extends Translation
{
    /**
     * Message string. Might use replacements like %foo%, which are replaced by
     * the values specified in the values array.
     *
     * @var string
     */
    protected $message;

    /**
     * Translation value objects. May not contain any numbers, which might
     * result in requiring plural forms. Use Plural for that.
     *
     * @var array
     */
    protected $values;

    /**
     * Construct singular only message from string and optional value array.
     *
     * @param string $message
     * @param array $values
     */
    public function __construct($message, array $values = [])
    {
        $this->message = $message;
        $this->values = $values;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return strtr($this->message, $this->values);
    }
}

class_alias(Message::class, 'eZ\Publish\API\Repository\Values\Translation\Message');
