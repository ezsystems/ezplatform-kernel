<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\Author;

use Ibexa\Core\FieldType\Value as BaseValue;

/**
 * Value for Author field type.
 */
class Value extends BaseValue
{
    /**
     * List of authors.
     *
     * @var \Ibexa\Core\FieldType\Author\AuthorCollection
     */
    public $authors;

    /**
     * Construct a new Value object and initialize with $authors.
     *
     * @param \Ibexa\Core\FieldType\Author\Author[] $authors
     */
    public function __construct(array $authors = [])
    {
        $this->authors = new AuthorCollection($authors);
    }

    public function __toString()
    {
        if (empty($this->authors)) {
            return '';
        }

        $authorNames = [];

        if ($this->authors instanceof AuthorCollection) {
            foreach ($this->authors as $author) {
                $authorNames[] = $author->name;
            }
        }

        return implode(', ', $authorNames);
    }
}

class_alias(Value::class, 'eZ\Publish\Core\FieldType\Author\Value');
