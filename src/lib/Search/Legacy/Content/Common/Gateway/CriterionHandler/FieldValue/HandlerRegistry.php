<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\FieldValue;

use OutOfBoundsException;

/**
 * Registry for Criterion field value handlers.
 */
class HandlerRegistry
{
    /**
     * Map of Criterion field value handlers where key is field type identifier
     * and value is field value handler.
     *
     * @var \Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\FieldValue\Handler[]
     */
    protected $map = [];

    /**
     * Create field value handler registry with handler map.
     *
     * @param \Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\FieldValue\Handler[] $map
     *        Map of Criterion field value handlers where key is field type identifier and value field value handler
     */
    public function __construct(array $map = [])
    {
        foreach ($map as $fieldTypeIdentifier => $handler) {
            $this->register($fieldTypeIdentifier, $handler);
        }
    }

    /**
     * Register $handler for $fieldTypeIdentifier.
     *
     * @param string $fieldTypeIdentifier
     * @param \Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\FieldValue\Handler $handler
     */
    public function register($fieldTypeIdentifier, $handler)
    {
        $this->map[$fieldTypeIdentifier] = $handler;
    }

    /**
     * Returns handler for given $fieldTypeIdentifier.
     *
     * @throws \OutOfBoundsException If handler is not registered for a given $fieldTypeIdentifier
     *
     * @param string $fieldTypeIdentifier
     *
     * @return \Ibexa\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\FieldValue\Handler
     */
    public function get($fieldTypeIdentifier)
    {
        if (!isset($this->map[$fieldTypeIdentifier])) {
            throw new OutOfBoundsException("No handler registered for Field Type '{$fieldTypeIdentifier}'.");
        }

        return $this->map[$fieldTypeIdentifier];
    }

    /**
     * Checks if handler is registered for the given $fieldTypeIdentifier.
     *
     * @param string $fieldTypeIdentifier
     *
     * @return bool
     */
    public function has($fieldTypeIdentifier)
    {
        return isset($this->map[$fieldTypeIdentifier]);
    }
}

class_alias(HandlerRegistry::class, 'eZ\Publish\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\FieldValue\HandlerRegistry');
