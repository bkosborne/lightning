<?php

namespace Acquia\LightningExtension\Context;

use Behat\Gherkin\Node\TableNode;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;
use Drupal\DrupalExtension\Context\MinkContext;

/**
 * A context for working with media entities and bundles.
 */
class MediaContext extends DrupalSubContextBase {

  /**
   * The Mink context.
   *
   * @var MinkContext
   */
  protected $minkContext;

  /**
   * The undo context.
   *
   * @var UndoContext
   */
  protected $undoContext;

  /**
   * Gathers required contexts.
   *
   * @BeforeScenario
   */
  public function gatherContexts() {
    $this->minkContext = $this->getContext(MinkContext::class);
    $this->undoContext = $this->getContext(UndoContext::class);
  }

  /**
   * Creates a media bundle.
   *
   * @param \Behat\Gherkin\Node\TableNode $values
   *   The form field values with which to create the media bundle.
   *
   * @Given a media bundle:
   *
   * @When I create a media bundle:
   */
  public function createMediaBundle(TableNode $values) {
    $this->visitPath('/admin/structure/media/add');

    foreach ($values->getHash() as $row) {
      foreach ($row as $field => $value) {
        $this->minkContext->fillField($field, $value);
      }
    }

    // Wait for the hidden ID field to be set by JavaScript. If this isn't a
    // JavaScript session, no harm done.
    sleep(2);

    $this->undoContext->push(
      [$this, 'deleteMediaBundle'],
      $this->assertSession()->fieldExists('id')->getValue()
    );

    $this->minkContext->pressButton('Save media bundle');
  }

  /**
   * Deletes a media bundle.
   *
   * @param string $id
   *   The media bundle ID.
   *
   * @When I delete the :id media bundle
   */
  public function deleteMediaBundle($id) {
    $this->visitPath('/admin/structure/media/manage/' . $id . '/delete');
    $this->minkContext->pressButton('Delete');
  }

}
