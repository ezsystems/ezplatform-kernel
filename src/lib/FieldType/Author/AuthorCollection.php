<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\Author;

use ArrayObject;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;

/**
 * Author collection.
 * This collection can only hold {@link \Ibexa\Core\FieldType\Author\Author} objects.
 */
class AuthorCollection extends ArrayObject
{
    /**
     * @param \Ibexa\Core\FieldType\Author\Author[] $elements
     */
    public function __construct(array $elements = [])
    {
        // Call parent constructor without $elements because all author elements
        // must be given an id by $this->offsetSet()
        parent::__construct();
        foreach ($elements as $i => $author) {
            $this->offsetSet($i, $author);
        }
    }

    /**
     * Adds a new author to the collection.
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType When $value is not of type Author
     *
     * @param int $offset
     * @param \Ibexa\Core\FieldType\Author\Author $value
     */
    public function offsetSet($offset, $value): void
    {
        if (!$value instanceof Author) {
            throw new InvalidArgumentType(
                '$value',
                Author::class,
                $value
            );
        }

        $aAuthors = $this->getArrayCopy();
        parent::offsetSet($offset, $value);
        if (!isset($value->id) || $value->id == -1) {
            if (!empty($aAuthors)) {
                $value->id = end($aAuthors)->id + 1;
            } else {
                $value->id = 1;
            }
        }
    }

    /**
     * Removes authors from current collection with a list of Ids.
     *
     * @param array $authorIds Author's Ids to remove from current collection
     */
    public function removeAuthorsById(array $authorIds)
    {
        $aAuthors = $this->getArrayCopy();
        foreach ($aAuthors as $i => $author) {
            if (in_array($author->id, $authorIds)) {
                unset($aAuthors[$i]);
            }
        }

        $this->exchangeArray($aAuthors);
    }
}

class_alias(AuthorCollection::class, 'eZ\Publish\Core\FieldType\Author\AuthorCollection');
