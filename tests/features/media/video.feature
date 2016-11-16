@lightning @media @api
Feature: Video media assets
  A media asset representing an externally hosted video.

  @javascript @clean-entities
  Scenario: Creating a video from a YouTube URL
    Given I am logged in as a user with the media_creator role
    When I visit "/media/add/video"
    And I enter "https://www.youtube.com/watch?v=zQ1_IbFFbzA" for "Video URL"
    And I wait for AJAX to finish
    And I enter "The Pill Scene" for "Media name"
    And I press "Save and publish"
    Then I should be visiting a media entity
    And this media should exist:
      | bundle | name           |
      | video  | The Pill Scene |

  @javascript @clean-entities
  Scenario: Creating a video from a Vimeo URL
    Given I am logged in as a user with the media_creator role
    When I visit "/media/add/video"
    And I enter "https://vimeo.com/14782834" for "Video URL"
    And I wait for AJAX to finish
    And I enter "Cache Rules Everything Around Me" for "Media name"
    And I press "Save and publish"
    Then I should be visiting a media entity
    And this media should exist:
      | bundle | name                             |
      | video  | Cache Rules Everything Around Me |

  Scenario: Viewing a video as an anonymous user
    Given video from embed code:
    """
    https://www.youtube.com/watch?v=ktCgVopf7D0
    """
    And I am an anonymous user
    When I visit a media entity of type video
    Then I should get a 200 HTTP response
