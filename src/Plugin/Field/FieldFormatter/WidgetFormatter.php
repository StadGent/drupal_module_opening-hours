<?php

namespace Drupal\opening_hours\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Display the openinghours field as an ajax widget.
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

    $types = new WidgetTypes();

    $preview_widget = FALSE;

    if ($this->getSetting('preview_widget_type')) {
      $preview_widget = [
        'label' => $types->getToggleLabelByType($this->getSetting('preview_widget_type')),
        'type' => $this->getSetting('preview_widget_type'),
      ];
    }

    $widgets[] = [
      'label' => $types->getToggleLabelByType($this->getSetting('widget_type')),
      'type' => $this->getSetting('widget_type'),
    ];

    if ($this->getSetting('alternative_widget_type')) {
      $widgets[] = [
        'label' => $types->getToggleLabelByType($this->getSetting('alternative_widget_type')),
        'type' => $this->getSetting('alternative_widget_type'),
      ];
    }

    foreach ($items as $delta => $item) {
      if (!$this->getSetting('single_widget') || $delta === 0) {
        $element[$delta] = [
          '#type' => 'opening_hours_widget',
          '#display_title' => $this->getSetting('display_title'),
          '#single_widget' => $this->getSetting('single_widget'),
          '#preview_widget' => $preview_widget,
          '#widgets' => $widgets,
          '#service_id' => $item->service,
          '#channel_id' => $item->channel,
        ];
      }
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $settings = [
      'widget_type' => 'week',
      'alternative_widget_type' => NULL,
      'preview_widget_type' => NULL,
      'display_title' => TRUE,
      'single_widget' => FALSE,
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

    $element['alternative_widget_type'] = [
      '#title' => $this->t('Alternative type'),
      '#description' => $this->t('Provides an alternative widget type to the visitor.'),
      '#type' => 'select',
      '#options' => $types->getList(),
      '#empty_option' => $this->t('None'),
      '#default_value' => $this->getSetting('alternative_widget_type'),
    ];

    $element['preview_widget_type'] = [
      '#title' => $this->t('Preview type'),
      '#description' => $this->t('Provides a preview widget type to the visitor.'),
      '#type' => 'select',
      '#options' => $types->getList(),
      '#empty_option' => $this->t('None'),
      '#default_value' => $this->getSetting('preview_widget_type'),
    ];

    $element['display_title'] = [
      '#title' => $this->t('Display title'),
      '#description' => $this->t('Displays the title of the channel when checked.'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('display_title'),
    ];

    $element['single_widget'] = [
      '#title' => $this->t('Display all channels in one widget.'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('single_widget'),
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

    if ($settings['alternative_widget_type']) {
      $label = $types->getLabelByType($settings['alternative_widget_type']);
      $summary[] = $this->t('Alternative widget: %type', ['%type' => $label]);
    }

    if ($settings['preview_widget_type']) {
      $label = $types->getLabelByType($settings['preview_widget_type']);
      $summary[] = $this->t('Preview widget: %type', ['%type' => $label]);
    }

    return $summary;
  }

}
