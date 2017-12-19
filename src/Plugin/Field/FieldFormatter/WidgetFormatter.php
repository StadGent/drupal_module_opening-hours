<?php

namespace Drupal\opening_hours\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
class WidgetFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

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
        '#type' => 'opening_hours_widget',
        '#widget_type' => $this->getSetting('widget_type'),
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
