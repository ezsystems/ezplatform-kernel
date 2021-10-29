<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\Author;

use Ibexa\Contracts\Core\Persistence\ValueObject;

/**
 * Value object for an author.
 */
class Author extends ValueObject
{
    /**
     * Author's Id in the collection that holds it.
     * If not set or -1, an Id will be generated when added to AuthorCollection.
     *
     * @var int
     */
    public $id;

    /**
     * Name of the author.
     *
     * @var string
     */
    public $name;

    /**
     * Email of the author.
     *
     * @var string
     */
    public $email;
}

class_alias(Author::class, 'eZ\Publish\Core\FieldType\Author\Author');
