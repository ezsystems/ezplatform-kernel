<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Bundle\EzPublishCoreBundle\Features\Context;

use Behat\MinkExtension\Context\RawMinkContext;
use PHPUnit\Framework\Assert;

class QueryControllerContext extends RawMinkContext
{
    /**
     * @Given /^the Query results are assigned to the "([^"]*)" twig variable$/
     */
    public function theQueryResultsAreAssignedToTheTwigVariable($twigVariableName)
    {
        $variableTypes = $this->getVariableTypesFromTemplate();

        Assert::assertArrayHasKey($twigVariableName, $variableTypes, "The $twigVariableName twig variable was not set");
    }

    /**
     * @Then the Query results assigned to the :arg1 twig variable is a :arg2 object
     */
    public function theQueryResultsAssignedToTheTwigVariableIsAObject($twigVariableName, $className)
    {
        $variableTypes = $this->getVariableTypesFromTemplate();

        Assert::assertArrayHasKey($twigVariableName, $variableTypes, "The $twigVariableName twig variable was not set");
        Assert::assertEquals($className, $variableTypes[$twigVariableName], "The $twigVariableName twig variable does not have $className type");
    }

    /**
     * @Then the Query results assigned to the twig variable is a Pagerfanta object and has limit :arg1 and selected page :arg2
     */
    public function theQueryResultsAssignedToTheTwigVariableIsAObjectAndHasLimitAndCountParams($pageLimit, $pageValue)
    {
        $pageLimitFound = false;
        $currentPageFound = false;

        $page = $this->getSession()->getPage();
        $maxPerPage = $page->findAll('css', 'div#maxPerPage');
        $currentPage = $page->findAll('css', 'div#currentPage');

        /** @var \Behat\Mink\Element\NodeElement $variableNode */
        foreach ($maxPerPage as $variableNode) {
            if ($variableNode->getText() === $pageLimit) {
                $pageLimitFound = true;
            }
        }

        /** @var \Behat\Mink\Element\NodeElement $valueNodes */
        foreach ($currentPage as $valueNode) {
            if ($valueNode->getText() === $pageValue) {
                $currentPageFound = true;
            }
        }

        Assert::assertTrue(
            $pageLimitFound,
            "The maxPerPage $pageLimit twig variable was not set"
        );

        Assert::assertTrue(
            $currentPageFound,
            "The currentPage $pageValue twig variable  was not set"
        );
    }

    /**
     * Returns an associative array with Twig variables as keys and their types as values.
     *
     * @return array
     */
    private function getVariableTypesFromTemplate(): array
    {
        $variableRows = $this->getSession()->getPage()->findAll('css', '.dump .item');

        $items = [];

        foreach ($variableRows as $row) {
            $variable = $row->find('css', '.variable')->getText();
            $type = $row->find('css', '.type')->getText();

            $items[$variable] = $type;
        }

        return $items;
    }
}
