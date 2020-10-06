<?php

namespace Drupal\opening_hours\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\opening_hours\Plugin\Field\FieldType\OpeningHoursItem;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Display the opening hours info by its field values.
 *
 * @FieldFormatter(
 *   id = "opening_hours_labels",
 *   label = @Translation("Labels"),
 *   field_types = {
 *     "opening_hours"
 *   }
 * )
 */
class LabelFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      $element[$delta] = [
        '#markup' => $this->createItemLabel($item),
      ];
    }

    return $element;
  }

  /**
   * Create the item label.
   *
   * @param \Drupal\opening_hours\Plugin\Field\FieldType\OpeningHoursItem $item
   *   The opening hours item to create the label for.
   *
   * @return string
   *   The generated item label.
   */
  protected function createItemLabel(OpeningHoursItem $item) {
    $value = $item->getValue();
    $tokens = [
      '[service:id]' => $value['service'],
      '[service:label]' => $value['service_label'],
      '[channel:id]' => $value['channel'],
      '[channel:label]' => $value['channel_label'],
      '[broken:status]' => $value['broken'] ? $this->t('Broken') : $this->t('OK'),
    ];

    return str_replace(
      array_keys($tokens),
      array_values($tokens),
      $this->getSetting('label_format')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $settings = [
      'label_format' => '[service:label] - [channel:label]',
    ];

    return $settings + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element['label_format'] = [
      '#title' => $this->t('Format'),
      '#description' => $this->t('Create a label by combining the available data.'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('label_format'),
    ];

    $element['label_placeholders'] = [
      'placeholders' => [
        '#theme' => 'item_list',
        '#items' => [
          '[service:id] ' . $this->t('The service ID'),
          '[service:label] ' . $this->t('The service label'),
          '[channel:id] ' . $this->t('The channel ID'),
          '[channel:label] ' . $this->t('The channel label'),
          '[broken:status] ' . $this->t('Is the link between the field and the backend broken.'),
        ],
      ],
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->t(
      'Format: %format',
      ['%format' => $this->getSetting('label_format')]
    );

    return $summary;
  }

}
