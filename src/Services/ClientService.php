<?php

namespace Drupal\opening_hours\Services;

use Drupal\Core\Config\ConfigFactoryInterface;
use GuzzleHttp\Client as GuzzleClient;
use StadGent\Services\OpeningHours\Client\Client as StadGentClient;
use StadGent\Services\OpeningHours\Configuration\Configuration;

/**
 * Client service to connect to the OpeningHours API.
 *
 * @package Drupal\opening_hours\Services
 */
class ClientService extends StadGentClient {

  /**
   * Class constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $config = $config_factory->get('opening_hours.settings');

    $configuration = new Configuration($config->get('endpoint'));
    $guzzleClient = new GuzzleClient(
      [
        'base_uri' => $configuration->getUri(),
      ]
    );

    parent::__construct($guzzleClient, $configuration);
  }

}
