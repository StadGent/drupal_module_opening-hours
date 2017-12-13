<?php

namespace Drupal\opening_hours\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

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
        '#type' => 'week',
        '#service_id' => $item->service,
        '#channel_id' => $item->channel,
      ];
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $settings = [
      'widget_type' => 'week',
    ];

    return $settings + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $types = new WidgetTypes();

    $element['widget_type'] = [
      '#title' => $this->t('Type'),
      '#type' => 'select',
      '#options' => $types->getList(),
      '#default_value' => $this->getSetting('widget_type'),
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $settings = $this->getSettings();

    $types = new WidgetTypes();
    $label = $types->getLabelByType($settings['widget_type']);

    $summary[] = $this->t('Show as %type widget', ['%type' => $label]);

    return $summary;
  }

}
