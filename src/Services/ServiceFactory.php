<?php

namespace Drupal\opening_hours\Services;

use StadGent\Services\OpeningHours\Channel;
use StadGent\Services\OpeningHours\ChannelOpeningHours;
use StadGent\Services\OpeningHours\ChannelOpeningHoursHtml;
use StadGent\Services\OpeningHours\Service;

/**
 * Factory to create the different Opening Hours Services.
 *
 * @package Drupal\opening_hours\Services
 */
class ServiceFactory {

  /**
   * Create the Service service.
   *
   * @param \Drupal\opening_hours\Services\ClientService $client
   *   The client service.
   *
   * @return \StadGent\Services\OpeningHours\Service\Service\ServiceService
   *   The Service Service.
   */
  public static function createServiceService(ClientService $client) {
    return Service::create($client);
  }

  /**
   * Create the Channel service.
   *
   * @param \Drupal\opening_hours\Services\ClientService $client
   *   The client service.
   *
   * @return \StadGent\Services\OpeningHours\Service\Channel\ChannelService
   *   The Channel service.
   */
  public static function createChannelService(ClientService $client) {
    return Channel::create($client);
  }

  /**
   * Create the Channel OpeningHours service.
   *
   * @param \Drupal\opening_hours\Services\ClientService $client
   *   The client service.
   *
   * @return \StadGent\Services\OpeningHours\Service\Channel\OpeningHoursService
   *   The Channel Opening Hours service.
   */
  public static function createChannelOpeningHoursService(ClientService $client) {
    return ChannelOpeningHours::create($client);
  }

  /**
   * Create the Channel OpeningHours HTML service.
   *
   * @param \Drupal\opening_hours\Services\ClientService $client
   *   The client service.
   *
   * @return \StadGent\Services\OpeningHours\Service\Channel\OpeningHoursHtmlService
   *   The Channel Opening Hours HTML service.
   */
  public static function createChannelOpeningHoursHtmlService(ClientService $client) {
    return ChannelOpeningHoursHtml::create($client);
  }

}
