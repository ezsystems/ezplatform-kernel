<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\SiteAccess\Matcher;

use Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest;
use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher;

abstract class Regex implements Matcher
{
    /**
     * Element that will be matched against the regex.
     *
     * @var string
     */
    protected $element;

    /**
     * Regular expression used for matching.
     *
     * @var string
     */
    protected $regex;

    /**
     * Item number to pick in regex.
     *
     * @var string
     */
    protected $itemNumber;

    /** @var \Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest */
    protected $request;

    /** @var string */
    protected $matchedSiteAccess;

    /**
     * Constructor.
     *
     * @param string $regex Regular Expression to use.
     * @param int $itemNumber Item number to pick in regex.
     */
    public function __construct($regex, $itemNumber)
    {
        $this->regex = $regex;
        $this->itemNumber = $itemNumber;
    }

    public function __sleep()
    {
        return ['regex', 'itemNumber', 'matchedSiteAccess'];
    }

    public function match()
    {
        return $this->getMatchedSiteAccess();
    }

    /**
     * Returns matched SiteAccess.
     *
     * @return string|bool
     */
    protected function getMatchedSiteAccess()
    {
        if (isset($this->matchedSiteAccess)) {
            return $this->matchedSiteAccess;
        }

        preg_match(
            "@{$this->regex}@",
            (string)$this->element,
            $match
        );

        $this->matchedSiteAccess = $match[$this->itemNumber] ?? false;

        return $this->matchedSiteAccess;
    }

    /**
     * Injects the request object to match against.
     *
     * @param \Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest $request
     */
    public function setRequest(SimplifiedRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Injects element to match against with the regexp.
     *
     * @param string $element
     */
    public function setMatchElement($element)
    {
        $this->element = $element;
    }
}

class_alias(Regex::class, 'eZ\Publish\Core\MVC\Symfony\SiteAccess\Matcher\Regex');
