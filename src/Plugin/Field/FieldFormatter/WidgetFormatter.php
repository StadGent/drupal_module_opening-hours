<?php

namespace Drupal\opening_hours\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Open Now formatter for the opening_hours field item.
 *
 * @FieldFormatter(
 *   id = "opening_hours_widget",
 *   label = @Translation("Widget"),
 *   field_types = {
 *     "opening_hours"
 *   }
 * )
 */
class WidgetFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      $element[$delta] = [
        '#theme' => 'opening_hours_widget',
        '#service_id' => $item->service,
        '#channel_id' => $item->channel,
      ];
    }

    return $element;
  }

}
