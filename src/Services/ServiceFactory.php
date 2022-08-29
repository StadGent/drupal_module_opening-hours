<?php

declare(strict_types=1);

namespace Drupal\opening_hours\Services;

use StadGent\Services\OpeningHours\Channel;
use StadGent\Services\OpeningHours\ChannelOpeningHours;
use StadGent\Services\OpeningHours\ChannelOpeningHoursHtml;
use StadGent\Services\OpeningHours\Client\Client;
use StadGent\Services\OpeningHours\Service;
use StadGent\Services\OpeningHours\Service\Channel\ChannelServiceInterface;
use StadGent\Services\OpeningHours\Service\Channel\OpeningHoursHtmlServiceInterface;
use StadGent\Services\OpeningHours\Service\Channel\OpeningHoursServiceInterface;
use StadGent\Services\OpeningHours\Service\Service\ServiceServiceInterface;

/**
 * Factory to create the different Opening Hours Services.
 *
 * @package Drupal\opening_hours\Services
 */
class ServiceFactory {

  /**
   * Create the Service service.
   *
   * @param \StadGent\Services\OpeningHours\Client\Client $client
   *   The client service.
   *
   * @return \StadGent\Services\OpeningHours\Service\Service\ServiceServiceInterface
   *   The Service Service.
   */
  public static function createServiceService(Client $client): ServiceServiceInterface {
    return Service::create($client);
  }

  /**
   * Create the Channel service.
   *
   * @param \StadGent\Services\OpeningHours\Client\Client $client
   *   The client service.
   *
   * @return \StadGent\Services\OpeningHours\Service\Channel\ChannelServiceInterface
   *   The Channel service.
   */
  public static function createChannelService(Client $client): ChannelServiceInterface {
    return Channel::create($client);
  }

  /**
   * Create the Channel OpeningHours service.
   *
   * @param \StadGent\Services\OpeningHours\Client\Client $client
   *   The client service.
   *
   * @return \StadGent\Services\OpeningHours\Service\Channel\OpeningHoursServiceInterface
   *   The Channel Opening Hours service.
   */
  public static function createChannelOpeningHoursService(Client $client): OpeningHoursServiceInterface {
    return ChannelOpeningHours::create($client);
  }

  /**
   * Create the Channel OpeningHours HTML service.
   *
   * @param \StadGent\Services\OpeningHours\Client\Client $client
   *   The client service.
   *
   * @return \StadGent\Services\OpeningHours\Service\Channel\OpeningHoursHtmlServiceInterface
   *   The Channel Opening Hours HTML service.
   */
  public static function createChannelOpeningHoursHtmlService(Client $client): OpeningHoursHtmlServiceInterface {
    return ChannelOpeningHoursHtml::create($client);
  }

}
