<?php

namespace Drupal\lightning_layout\Plugin\Field\FieldWidget;

use Drupal\Core\Entity\Entity\EntityViewMode;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\panelizer\Plugin\Field\FieldWidget\PanelizerWidget as BasePanelizerWidget;

/**
 * Implementation of panelizer widget plugin supporting view mode descriptions.
 */
class PanelizerWidget extends BasePanelizerWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    foreach (Element::children($element) as $i) {
      if (empty($element[$i]['default']['#description'])) {
        $view_mode = $items->getEntity()->getEntityTypeId() . '.' . $element[$i]['view_mode']['#value'];

        $element[$i]['default']['#description'] = EntityViewMode::load($view_mode)
          ->getThirdPartySetting('lightning_core', 'description');
      }
    }

    return $element;
  }

}
