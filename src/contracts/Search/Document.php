<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Search;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * Base class for documents.
 */
class Document extends ValueObject
{
    /**
     * Id of the document.
     *
     * @var string
     */
    public $id;

    /**
     * Translation language code that the documents represents.
     *
     * @var string
     */
    public $languageCode;

    /**
     * Denotes that document's translation is the main translation and it is
     * always available.
     *
     * @var bool
     */
    public $alwaysAvailable;

    /**
     * Denotes that document's translation is a main translation of the Content.
     *
     * @var bool
     */
    public $isMainTranslation;

    /**
     * An array of fields.
     *
     * @var \Ibexa\Contracts\Core\Search\Field[]
     */
    public $fields = [];

    /**
     * An array of sub-documents.
     *
     * @var \Ibexa\Contracts\Core\Search\Document[]
     */
    public $documents = [];
}

class_alias(Document::class, 'eZ\Publish\SPI\Search\Document');
