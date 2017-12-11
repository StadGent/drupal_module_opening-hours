<?php

namespace Drupal\opening_hours\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Open Now formatter for the opening_hours field item.
 *
 * @FieldFormatter(
 *   id = "opening_hours_opennow",
 *   label = @Translation("Open now"),
 *   field_types = {
 *     "opening_hours"
 *   }
 * )
 */
class OpenNowFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      $element[$delta] = [
        '#theme' => 'opening_hours_opennow',
        '#serviceId' => $item->service,
        '#channelId' => $item->channel,
      ];
    }

    return $element;
  }

}
