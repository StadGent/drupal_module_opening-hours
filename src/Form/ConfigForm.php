<?php

namespace Drupal\opening_hours\Form;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form to configure the Opening Hours settings.
 */
class ConfigForm extends ConfigFormBase {

  /**
   * Constructs a ConfigForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $stringTranslation
   *   The String translations.
   */
  public function __construct(ConfigFactoryInterface $config_factory, TranslationInterface $stringTranslation) {
    parent::__construct($config_factory);
    $this->setStringTranslation($stringTranslation);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('string_translation')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'opening_hours_config';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('opening_hours.settings');

    $form['endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Endpoint URL'),
      '#description' => $this->t('Provide the endpoint URL including the API version number.'),
      '#default_value' => $config->get('endpoint'),
      '#required' => TRUE,
    ];

    $form['cache_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable cache'),
      '#description' => $this->t('This will enable caching of the responses from the API.'),
      '#default_value' => $config->get('cache_enabled'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (!UrlHelper::isValid($form_state->getValue('endpoint'), TRUE)) {
      $form_state->setErrorByName(
        'endpoint',
        $this->t('Provide an absolute URL.')
      );
    }
    else {
      $form_state->setValue(
        'endpoint',
        rtrim($form_state->getValue('endpoint'), '/') . '/'
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('opening_hours.settings')
      ->set('endpoint', $form_state->getValue('endpoint'))
      ->set('cache_enabled', (int) $form_state->getValue('cache_enabled'))
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['opening_hours.settings'];
  }

}
