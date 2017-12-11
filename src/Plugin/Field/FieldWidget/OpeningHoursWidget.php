<?php

namespace Drupal\opening_hours\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A widget bar.
 *
 * @FieldWidget(
 *   id = "opening_hours",
 *   label = @Translation("Opening Hours"),
 *   field_types = {
 *     "opening_hours"
 *   }
 * )
 */
class OpeningHoursWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs a Opening Hours widget object.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The widget settings.
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
    array $third_party_settings,
    TranslationInterface $translation
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->setStringTranslation($translation);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    /* @var $stringTranslation TranslationInterface */
    $stringTranslation = $container->get('string_translation');

    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $stringTranslation
    );
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $element['opening_hours'] = [
      '#title' => $this->t('Opening hours'),
      '#type' => 'details',
      '#open' => TRUE,
    ];

    $service_default = isset($items[$delta]->service)
      ? $items[$delta]->service
      : NULL;
    $element['opening_hours']['service'] = [
      '#title' => $this->t('Service'),
      '#type' => 'textfield',
      '#default' => $service_default,
    ];

    $channel_default = isset($items[$delta]->channel)
      ? $items[$delta]->channel
      : NULL;
    $element['opening_hours']['channel'] = [
      '#title' => $this->t('Channel'),
      '#type' => 'textfield',
      '#default' => $channel_default,
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as $delta => $data) {
      $values[$delta]['channel'] = (int) $data['opening_hours']['channel'];
      $values[$delta]['service'] = (int) $data['opening_hours']['service'];
      unset($values[$delta]['opening_hours']);
    }

    return $values;
  }

  /**
   * Validate the fields.
   *
   * This will check if a URL is set if a label is filled in.
   *
   * @param array $element
   *   The form values container.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function validate(array $element, FormStateInterface $form_state) {
    // If no channel set, clear all field values.
    if (empty($element['service']['#value'])) {
      $form_state->setValueForElement($element['service'], NULL);
      $form_state->setValueForElement($element['channel'], NULL);
      return;
    }
  }

}
