<?php

namespace Drupal\opening_hours\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\StringTranslation\TranslationInterface;
use StadGent\Services\OpeningHours\Service\Service\ServiceService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ServiceController extends ControllerBase {
  /**
   * The Opening Hours Service service.
   *
   * @var \StadGent\Services\OpeningHours\Service\Service\ServiceService
   */
  protected $serviceService;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\StringTranslation\TranslationInterface $stringTranslation
   *   The String translation service.
   * @param \StadGent\Services\OpeningHours\Service\Service\ServiceService $serviceService
   *   The Opening hours Service service.
   */
  public function __construct(TranslationInterface $stringTranslation, ServiceService $serviceService) {
    $this->setStringTranslation($stringTranslation);
    $this->serviceService = $serviceService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /* @var $stringTranslation \Drupal\Core\StringTranslation\TranslationInterface */
    $stringTranslation = $container->get('string_translation');

    /* @var $serviceService \StadGent\Services\OpeningHours\Service\Service\ServiceService */
    $serviceService = $container->get('opening_hours.service');

    return new static($stringTranslation, $serviceService);
  }

  /**
   * Lookup Services through an autocomplete field.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request object containing the search string.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A JSON response containing the autocomplete suggestions.
   */
  public function autocomplete(Request $request) {
    $matches = [];

    $search = $request->query->get('q');
    $services = $this->serviceService->searchByLabel($search);
    /* @var $service \StadGent\Services\OpeningHours\Value\Service */
    foreach ($services as $service) {
      $value = sprintf('%s [%d]', $service->getLabel(), $service->getId());

      $matches[] = [
        'value' => $value,
        'label' => $service->getLabel(),
      ];
    }

    return new JsonResponse($matches);
  }

}
