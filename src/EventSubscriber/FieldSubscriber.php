<?php

namespace Drupal\opening_hours\EventSubscriber;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\opening_hours\Event\FieldBrokenLinkEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Logs the creation of a new node.
 */
class FieldSubscriber implements EventSubscriberInterface {

  /**
   * The logger service.
   *
   * @var \Psr\Log\LoggerInterface
   */
  private $logger;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerfFactory
   *   The logger service.
   */
  public function __construct(LoggerChannelFactoryInterface $loggerfFactory) {
    $this->logger = $loggerfFactory->get('opening_hours');
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      FieldBrokenLinkEvent::NAME => ['onFieldBrokenLinkWriteToLog'],
    ];
  }

  /**
   * Write to the log when an opening hours field link is broken.
   *
   * @param \Drupal\opening_hours\Event\FieldBrokenLinkEvent $event
   *   The event to log.
   */
  public function onFieldBrokenLinkWriteToLog(FieldBrokenLinkEvent $event) {
    $entity = $event->getEntity();

    $this
      ->logger
      ->error(
        'Entity @entity_label (@entity_type:@entity_id) has an Opening Hours value (@field:@field_delta) which no longer exists in the Opening Hours backend.',
        [
          '@entity_label' => $entity->label(),
          '@entity_type' => $entity->getEntityTypeId(),
          '@entity_id' => $entity->id(),
          '@field' => $event->getFieldName(),
          '@field_delta' => $event->getDelta(),
        ]
      );
  }

}
