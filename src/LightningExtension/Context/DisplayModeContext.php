<?php

namespace Acquia\LightningExtension\Context;

use Behat\Gherkin\Node\TableNode;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;
use Drupal\DrupalExtension\Context\MinkContext;

/**
 * A context for working with entity display modes.
 */
class DisplayModeContext extends DrupalSubContextBase {

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
   * Creates a view mode.
   *
   * @param string $entity_type
   *   The entity type ID to which the view mode applies.
   * @param \Behat\Gherkin\Node\TableNode $values
   *   The form field values with which to create the view mode.
   *
   * @When I create a(n) :entity_type view mode:
   *
   * @Given a(n) :entity_type view mode:
   */
  public function createViewMode($entity_type, TableNode $values) {
    $this->visitPath('/admin/structure/display-modes/view/add/' . $entity_type);

    foreach ($values->getHash() as $row) {
      foreach ($row as $field => $value) {
        $this->minkContext->fillField($field, $value);
      }
    }

    // Wait for the hidden ID field to be set by JavaScript. If this isn't a
    // JavaScript session, no harm done.
    sleep(2);

    $this->undoContext->push(
      [$this, 'deleteViewMode'],
      $entity_type . '.' . $this->assertSession()->fieldExists('id')->getValue()
    );

    $this->minkContext->pressButton('Save');
  }

  /**
   * Deletes a view mode.
   *
   * @param string $id
   *   The view mode ID (e.g., node.foo).
   *
   * @When I delete the :id view mode
   */
  public function deleteViewMode($id) {
    $this->visitPath('/admin/structure/display-modes/view/manage/' . $id . '/delete');
    $this->minkContext->pressButton('Delete');
  }

}
