@setup
Feature: Query controller
  In order to simplify listing items from the repository
  As a developer
  I want to run repository queries from content views

    @APIUser:admin
    Scenario: Set up Query Controller tests
      Given I create "Folder" Content items in root in "eng-GB"
      | name                     | short_name               |
      | QueryControllerContainer | QueryControllerContainer |
      And I create "Folder" Content items in "QueryControllerContainer" in "eng-GB"
      | name                 | short_name           |
      | QueryControllerItem1 | QueryControllerItem1 |
      | QueryControllerItem2 | QueryControllerItem2 |
      | QueryControllerItem3 | QueryControllerItem3 |
      | QueryControllerItem4 | QueryControllerItem4 |
      And  I append configuration to "default" siteaccess under "content_view.full" key
        """
            query_controller_item_1:
                template: "@eZBehat/tests/dump.html.twig"
                match:
                    Id\Location: "%location_id(QueryControllerContainer/QueryControllerItem1)%"
                controller: ez_query:locationQueryAction
                params:
                    query:
                        query_type: 'LocationChildren'
                        parameters:
                            parentLocationId: 2
                        assign_results_to: 'children'
            query_controller_item_2:
                template: "@eZBehat/tests/dump.html.twig"
                match:
                    Id\Location: "%location_id(QueryControllerContainer/QueryControllerItem2)%"
                controller: ez_query:pagingQueryAction
                params:
                    query:
                        query_type: 'LocationChildren'
                        parameters:
                            parentLocationId: 2
                        assign_results_to: 'children'
            query_controller_item_3:
                template: tests.html.twig
                match:
                    Id\Location: "%location_id(QueryControllerContainer/QueryControllerItem3)%"
                controller: ez_query:pagingQueryAction
                params:
                    query:
                        query_type: 'LocationChildren'
                        parameters:
                            parentLocationId: 2
                        limit: 1
                        assign_results_to: 'children'
            query_controller_item_4:
                template: tests.html.twig
                match:
                    Id\Location: "%location_id(QueryControllerContainer/QueryControllerItem4)%"
                controller: ez_query:pagingQueryAction
                params:
                    query:
                        query_type: 'LocationChildren'
                        parameters:
                            parentLocationId: 2
                        page_param: p
                        limit: 1
                        assign_results_to: 'children'
          """
      And I create a file "src/QueryType/LocationChildrenQueryType.php" with contents
          """
          <?php
          namespace App\QueryType;

          use eZ\Publish\API\Repository\Values\Content\LocationQuery;
          use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ParentLocationId;
          use eZ\Publish\Core\QueryType\QueryType;

          class LocationChildrenQueryType implements QueryType
          {
              public function getQuery(array $parameters = [])
              {
                  return new LocationQuery([
                      'filter' => new ParentLocationId($parameters['parentLocationId']),
                  ]);
              }

              public function getSupportedParameters()
              {
                  return ['parentLocationId'];
              }

              public static function getName()
              {
                  return 'LocationChildren';
              }
          }
          """
      And I create a file "templates/tests.html.twig" with contents
          """
          <div id='currentPage'>{{ children.currentPage }}</div>
          <div id='maxPerPage'>{{ children.maxPerPage }}</div>
          """
