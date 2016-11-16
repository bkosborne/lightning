@lightning @media @api
Feature: Image media assets
  A media asset representing a locally hosted image.

  @javascript @clean-entities
  Scenario: Creating an image
    Given I am logged in as a user with the media_creator role
    When I visit "/media/add/image"
    And I attach the file "puppy.jpg" to "Image"
    And I wait for AJAX to finish
    And I enter "Foobaz" for "Media name"
    And I press "Save and publish"
    Then this media should exist:
      | bundle | name   |
      | image  | Foobaz |

  @javascript @clean-entities
  Scenario: Uploading an image to be ignored by the media library
    Given I am logged in as a user with the media_creator role
    When I visit "/media/add/image"
    And I attach the file "puppy.jpg" to "Image"
    And I wait for AJAX to finish
    And I enter "Blorg" for "Media name"
    And I uncheck the box "Save to my media library"
    And I press "Save and publish"
    And I visit "/entity-browser/iframe/media_browser"
    Then I should see "There are no media items to display."
    And this media should exist:
      | bundle | name  |
      | image  | Blorg |
