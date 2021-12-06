<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Persistence\Legacy\Content\Type\ContentUpdater;

use Ibexa\Core\Persistence\Legacy\Content\Gateway as ContentGateway;

/**
 * Updater action base class.
 */
abstract class Action
{
    /**
     * Content gateway.
     *
     * @var \Ibexa\Core\Persistence\Legacy\Content\Gateway
     */
    protected $contentGateway;

    /**
     * Creates a new action.
     *
     * @param \Ibexa\Core\Persistence\Legacy\Content\Gateway $contentGateway
     */
    public function __construct(ContentGateway $contentGateway)
    {
        $this->contentGateway = $contentGateway;
    }

    /**
     * Applies the action to the given $content.
     *
     * @param int $contentId
     */
    abstract public function apply($contentId);
}

class_alias(Action::class, 'eZ\Publish\Core\Persistence\Legacy\Content\Type\ContentUpdater\Action');
