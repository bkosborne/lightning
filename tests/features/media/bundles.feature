@lightning @media @api
Feature: Media bundles

  Scenario: Automatically attaching the "Save to my media library" field to new media bundles
    Given I am logged in as a user with the administrator role
    And a media bundle:
      | id     | Label  |
      | foobaz | Foobaz |
    When I visit "/media/add"
    And I click "Foobaz"
    Then I should see "Save to my media library"
