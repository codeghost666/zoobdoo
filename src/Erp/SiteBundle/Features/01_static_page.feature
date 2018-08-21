Feature: Static pages
  Scenario: Homepage
    Given I am Anonymous User
    When I send a GET request to "/"
    Then the response status code should be 200
