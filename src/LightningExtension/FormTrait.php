<?php

namespace Acquia\LightningExtension;

/**
 * Provides helper methods for interacting with forms.
 */
trait FormTrait {

  /**
   * Sets a form field generically.
   *
   * @param string $field
   *   The field locator (label, ID, name, etc).
   * @param mixed $value
   *   The value to set.
   */
  protected function setValue($field, $value) {
    /** @var \Behat\Mink\Element\NodeElement $field */
    $field = $this->assertSession()->fieldExists($field);

    if ($field->getTagName() == 'input') {
      // TODO: Handle radio buttons...?
      switch ($field->getAttribute('type')) {
        case 'checkbox':
          return $value ? $field->check() : $field->uncheck();

        default:
          break;
      }
    }
    $field->setValue($value);
  }

}
