<?php

namespace Drupal\opening_hours\Element;

use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Element\RenderElement;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the opening_hours_widget element.
 *
 * @RenderElement("opening_hours_widget")
 */
class OpeningHoursWidget extends RenderElement implements ContainerFactoryPluginInterface {

  /**
   * The opening hours configuration.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $openingHoursConfig;

  /**
   * OpeningHoursWidget constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ImmutableConfig $opening_hours_config
   *   The Opening hours configuration.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    ImmutableConfig $opening_hours_config
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->openingHoursConfig = $opening_hours_config;
  }

  /**
   * Dependency Injection.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   DI container.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
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

    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $openingHoursConfig
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#theme' => 'opening_hours_widget',
      '#type' => NULL,
      '#endpoint' => $this->openingHoursConfig->get('endpoint'),
      '#widget_id' => NULL,
      '#service_id' => NULL,
      '#channel_id' => NULL,
      '#date' => NULL,
      '#from' => NULL,
      '#until' => NULL,
      '#pre_render' => [
        [$class, 'preRenderMyElement'],
      ],
    ];
  }

  /**
   * Prepare the render array for the template.
   */
  public static function preRenderMyElement($element) {
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();

    // Attach widget + endpoint configuration.
    $element['#attached']['library'][] = 'opening_hours/widget';
    $element['#attached']['drupalSettings']['openingHours']['endpoint'] = $element['#endpoint'];
    $element['#attached']['drupalSettings']['openingHours']['language'] = $language;

    return $element;
  }

}
