<?php

declare(strict_types=1);

namespace Drupal\opening_hours\Services;

use Drupal\Core\Config\ConfigFactoryInterface;
use GuzzleHttp\Client as GuzzleClient;
use StadGent\Services\OpeningHours\Client\Client;
use StadGent\Services\OpeningHours\Configuration\Configuration;

/**
 * Client service to connect to the OpeningHours API.
 *
 * @package Drupal\opening_hours\Services
 */
final class ClientFactory {

  /**
   * Class constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public static function create(ConfigFactoryInterface $config_factory): Client {
    $config = $config_factory->get('opening_hours.settings');

    $configuration = new Configuration(
      $config->get('endpoint'),
      $config->get('key')
    );

    $guzzleClient = new GuzzleClient(['base_uri' => $configuration->getUri()]);

    return new Client($guzzleClient, $configuration);
  }

}
