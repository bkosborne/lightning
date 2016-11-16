<?php

namespace Acquia\LightningExtension\Context;

use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ExpectationException;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;

/**
 * A context for working with entities generically.
 */
class EntityContext extends DrupalSubContextBase {

  /**
   * Entities to be cleaned up after the scenario, keyed by entity type.
   *
   * @var array
   */
  protected $clean = [];

  /**
   * Deletes entities marked for cleanup.
   *
   * This only takes effect if the scenario has the @clean-entities tag.
   *
   * @param AfterScenarioScope $scope
   *   The hook scope.
   *
   * @AfterScenario
   */
  public function clean(AfterScenarioScope $scope) {
    if (in_array('clean-entities', $scope->getScenario()->getTags()) == FALSE) {
      return;
    }

    foreach ($this->clean as $entity_type => $entities) {
      $storage = \Drupal::entityTypeManager()->getStorage($entity_type);

      if (method_exists($storage, 'purge')) {
        $storage->purge($entities);
      }
      else {
        $storage->delete($entities);
      }
    }
  }

  /**
   * Asserts that entities matching certain criteria exist.
   *
   * @param string $entity_type
   *   The entity type ID.
   * @param \Behat\Gherkin\Node\TableNode $entities
   *   Sets of properties by which to load the entities.
   *
   * @throws ExpectationException
   *   If no entities match the criteria.
   *
   * @Then these :entity_type entities should exist:
   * @Then this :entity_type should exist:
   */
  public function assertEntities($entity_type, TableNode $entities) {
    if (empty($this->clean[$entity_type])) {
      $this->clean[$entity_type] = [];
    }

    $storage = \Drupal::entityTypeManager()->getStorage($entity_type);

    foreach ($entities->getHash() as $properties) {
      $entities = $storage->loadByProperties($properties);

      if ($entities) {
        $this->clean[$entity_type] = array_merge($this->clean[$entity_type], $entities);
      }
      else {
        throw new ExpectationException(
          'Expected at least one entity to match criteria: ' . print_r($properties, TRUE),
          $this->getSession()->getDriver()
        );
      }
    }
  }

}
