Feature: Query controller
    In order to simplify listing items from the repository
    As a developer
    I want to run repository queries from content views

Scenario: A content view can be configured to run and render a query
     When I go to "QueryControllerContainer/QueryControllerItem1"
      And the Query results are assigned to the "children" twig variable

Scenario: A content view can be configured to run and render a query and return a PagerFanta Object
  When I go to "QueryControllerContainer/QueryControllerItem2"
  Then the Query results assigned to the "children" twig variable is a "eZ\Publish\Core\Pagination\Pagerfanta\Pagerfanta" object

Scenario: A content view can be configured to run and render a query return a PagerFanta Object and set limit and page name
  When I go to "QueryControllerContainer/QueryControllerItem3"
  Then the Query results assigned to the twig variable is a Pagerfanta object and has limit "1" and selected page "1"

  @APIUser:admin
Scenario: A content view can be configured to run and render a query and set a specific page
    Given I create "Folder" Content items in root in "eng-GB"
    | short_name          |
    | TestPaginationItem1 |
    | TestPaginationItem2 |
    | TestPaginationItem3 |
  When I go to "QueryControllerContainer/QueryControllerItem4?p=2"
  Then the Query results assigned to the twig variable is a Pagerfanta object and has limit "1" and selected page "2"
