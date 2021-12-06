<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\Event;

use Exception;
use Ibexa\Core\MVC\Symfony\View\View;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * This event is dispatched when an Exception from eZ Publish API is thrown and could not be caught before.
 * It allows you to handle this exception and affect a specific Response for it.
 */
class APIContentExceptionEvent extends Event
{
    /** @var \Exception */
    private $apiException;

    /** @var \Ibexa\Core\MVC\Symfony\View\View */
    private $contentView;

    /** @var array */
    private $contentMeta;

    public function __construct(Exception $apiException, array $contentMeta)
    {
        $this->apiException = $apiException;
        $this->contentMeta = $contentMeta;
    }

    /**
     * @return \Exception
     */
    public function getApiException()
    {
        return $this->apiException;
    }

    /**
     * Injects the ContentView object to display content from.
     * It is a good idea to call {@link stopPropagation()} after that so that other listeners won't override it.
     *
     * @param \Ibexa\Core\MVC\Symfony\View\View $contentView
     */
    public function setContentView(View $contentView)
    {
        $this->contentView = $contentView;
    }

    /**
     * @return \Ibexa\Core\MVC\Symfony\View\View
     */
    public function getContentView()
    {
        return $this->contentView;
    }

    /**
     * @return bool
     */
    public function hasContentView()
    {
        return isset($this->contentView);
    }

    /**
     * Returns an array of metadata concerning the content that failed to load through API.
     * This array includes:
     *  - contentId Content Id when applicable (not available if a location was looked up)
     *  - locationId Location Id when applicable (not available if a content was looked up)
     *  - viewType full/line/...
     *
     * @return array
     */
    public function getContentMeta()
    {
        return $this->contentMeta;
    }
}

class_alias(APIContentExceptionEvent::class, 'eZ\Publish\Core\MVC\Symfony\Event\APIContentExceptionEvent');
