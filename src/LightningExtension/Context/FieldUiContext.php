<?php

namespace Acquia\LightningExtension\Context;

use Behat\Gherkin\Node\PyStringNode;
use Drupal\Component\Serialization\Yaml;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;
use Drupal\DrupalExtension\Context\MinkContext;

/**
 * A context for interacting with the Field UI generically.
 */
class FieldUiContext extends DrupalSubContextBase {

  /**
   * The Mink context.
   *
   * @var MinkContext
   */
  protected $minkContext;

  /**
   * Gathers required contexts.
   *
   * @BeforeScenario
   */
  public function gatherContexts() {
    $this->minkContext = $this->getContext(MinkContext::class);
  }

  /**
   * Creates a field through the UI.
   *
   * @param string $field_type
   *   The field type.
   * @param \Behat\Gherkin\Node\PyStringNode $settings
   *   The field settings. Should be a single YAML-encoded string containing a
   *   a 'storage' array of values for the storage form, then optionally
   *   storage_settings and field_settings value arrays for those forms,
   *   respectively.
   *
   * @When I create a(n) :field_type field:
   */
  public function createField($field_type, PyStringNode $settings) {
    $this->minkContext->clickLink('Add field');
    $this->minkContext->fillField('new_storage_type', $field_type);

    $settings = Yaml::decode($settings->getRaw());

    foreach ($settings['storage'] as $field => $value) {
      $this->minkContext->fillField($field, $value);
    }
    sleep(2);
    $this->minkContext->pressButton('Save and continue');

    if (isset($settings['storage_settings'])) {
      foreach ($settings['storage_settings'] as $field => $value) {
        $this->minkContext->fillField($field, $value);
      }
    }
    $this->minkContext->pressButton('Save field settings');

    if (isset($settings['field_settings'])) {
      foreach ($settings['storage_settings'] as $field => $value) {
        $this->minkContext->fillField($field, $value);
      }
    }
    $this->minkContext->pressButton('Save settings');
  }

}
