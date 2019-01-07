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
class LabelFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs a EntranceFeeFormatter object.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $translation
   *   The String translation.
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    $label,
    $view_mode,
    array $third_party_settings,
    TranslationInterface $translation
  ) {
    parent::__construct(
      $plugin_id,
      $plugin_definition,
      $field_definition,
      $settings,
      $label,
      $view_mode,
      $third_party_settings
    );

    $this->setStringTranslation($translation);
  }

  /**
   * Dependency Injection.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   DI container.
   * @param array $configuration
   *   Formatter configuration.
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   *
   * @return static
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    /* @var $stringTranslation \Drupal\Core\StringTranslation\TranslationInterface */
    $stringTranslation = $container->get('string_translation');

    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $stringTranslation
    );
  }

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
