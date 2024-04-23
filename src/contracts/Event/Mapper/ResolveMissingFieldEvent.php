<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Event\Mapper;

use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;
use Symfony\Contracts\EventDispatcher\Event;

final class ResolveMissingFieldEvent extends Event
{
    /** @var \eZ\Publish\SPI\Persistence\Content */
    private $content;

    /** @var \eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition */
    private $fieldDefinition;

    /** @var string */
    private $languageCode;

    /** @var array<mixed> */
    private $context;

    /** @var \eZ\Publish\SPI\Persistence\Content\Field|null */
    private $field;

    /**
     * @param array<mixed> $context
     */
    public function __construct(
        Content $content,
        FieldDefinition $fieldDefinition,
        string $languageCode,
        array $context = []
    ) {
        $this->content = $content;
        $this->fieldDefinition = $fieldDefinition;
        $this->languageCode = $languageCode;
        $this->context = $context;
        $this->field = null;
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function getFieldDefinition(): FieldDefinition
    {
        return $this->fieldDefinition;
    }

    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    /**
     * @return array<mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }

    public function setField(?Field $field): void
    {
        $this->field = $field;
    }

    public function getField(): ?Field
    {
        return $this->field;
    }
}
