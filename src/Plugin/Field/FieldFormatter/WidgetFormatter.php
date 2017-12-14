<?php

namespace Drupal\opening_hours\Plugin\Field\FieldFormatter;

use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
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
   * The opening hours configuration.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $openingHoursConfig;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

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
   * @param \Drupal\Core\Config\ImmutableConfig $opening_hours_config
   *   The Opening hours configuration.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager to get the current language from.
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
    ImmutableConfig $opening_hours_config,
    LanguageManagerInterface $language_manager,
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

    $this->openingHoursConfig = $opening_hours_config;
    $this->languageManager = $language_manager;
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
    /* @var $openingHoursConfig \Drupal\Core\Config\ImmutableConfig */
    $openingHoursConfig = $container
      ->get('config.factory')
      ->get('opening_hours.settings');

    /* @var $languageManager \Drupal\Core\Language\LanguageManagerInterface */
    $languageManager = $container->get('language_manager');

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
      $openingHoursConfig,
      $languageManager,
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
        '#theme' => 'opening_hours_widget',
        '#type' => $this->getSetting('widget_type'),
        '#service_id' => $item->service,
        '#channel_id' => $item->channel,
        '#language' => $this->languageManager->getCurrentLanguage()->getId(),
      ];
    }

    // Attach widget + endpoint configuration.
    $element['#attached']['library'][] = 'opening_hours/widget';
    $element['#attached']['drupalSettings']['openingHours']['endpoint'] = $this
      ->openingHoursConfig
      ->get('endpoint');
    $element['#attached']['drupalSettings']['openingHours']['language'] = $this
      ->languageManager
      ->getCurrentLanguage()
      ->getId();

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
