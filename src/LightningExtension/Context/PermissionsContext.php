<?php

namespace Acquia\LightningExtension\Context;

use Behat\Gherkin\Node\TableNode;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;
use Drupal\DrupalExtension\Context\MinkContext;

/**
 * A context for working with user roles and permissions.
 */
class PermissionsContext extends DrupalSubContextBase {

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
   * Creates a user role.
   *
   * @param \Behat\Gherkin\Node\TableNode $values
   *   The form field values with which to create the role.
   *
   * @When I create a role:
   *
   * @Given a role:
   */
  public function createRole(TableNode $values) {
    $this->visitPath('/admin/people/roles/add');

    foreach ($values->getHash() as $row) {
      foreach ($row as $field => $value) {
        $this->minkContext->fillField($field, $value);
      }
    }

    // Wait for the hidden ID field to be set by JavaScript. If this isn't a
    // JavaScript session, no harm done.
    sleep(2);

    $this->undoContext->push(
      [$this, 'deleteRole'],
      $this->assertSession()->fieldExists('id')->getValue()
    );

    $this->minkContext->pressButton('Save');
  }

  /**
   * Deletes a role.
   *
   * @param string $id
   *   The role ID.
   *
   * @When I delete the :id role
   */
  public function deleteRole($id) {
    $this->visitPath('/admin/people/roles/manage/' . $id . '/delete');
    $this->minkContext->pressButton('Delete');
  }

}
