@lightning @api
Feature: Lightning Roles and related config

  Scenario: Administrator Role select list should be present in Account Settings
    Given I am logged in as a user with the administrator role
    When I visit "/admin/config/people/accounts"
    Then I should see "This role will be automatically assigned new permissions whenever a module is enabled."

  Scenario: Describing a role
    Given I am logged in as a user with the "access administration pages,administer users,administer permissions" permissions
    And a role:
      | id     | Role name | Description    |
      | foobaz | Foobaz    | I am godd here |
    When I visit "/user"
    And I click "Edit"
    And I press "Save"
    Then I should see "I am godd here"
