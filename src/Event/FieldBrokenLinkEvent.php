<?php

namespace Drupal\opening_hours\Event;

use Drupal\Core\Entity\ContentEntityInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event when a link between an Opening Hours field and the backend is broken.
 *
 * An opening hours field is linked by the service & channel id.
 * The sync service will check if these services and their channels still exists
 * in the backend.
 * If the link no longer exists, this event will be triggered.
 */
class FieldBrokenLinkEvent extends Event {

  const NAME = 'opening_hours.field_broken_link';

  /**
   * The content entity.
   *
   * @var \Drupal\Core\Entity\ContentEntityInterface
   */
  private $entity;

  /**
   * The field name that contains the broken link.
   *
   * @var string
   */
  private $fieldName;

  /**
   * The field delta containing the broken link.
   *
   * @var int
   */
  private $delta;

  /**
   * Constructs a broken field link event.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity the field belongs to.
   * @param string $fieldName
   *   The field name that has the broken link.
   * @param int $delta
   *   The field delta.
   */
  public function __construct(ContentEntityInterface $entity, $fieldName, $delta) {
    $this->entity = $entity;
    $this->fieldName = $fieldName;
    $this->delta = (int) $delta;
  }

  /**
   * Get the inserted entity.
   *
   * @return \Drupal\Core\Entity\ContentEntityInterface
   *   The entity with the broken link.
   */
  public function getEntity() {
    return $this->entity;
  }

  /**
   * Get the field name containing the broken link.
   *
   * @return string
   *   The field name.
   */
  public function getFieldName() {
    return $this->fieldName;
  }

  /**
   * Get the value delta.
   *
   * @return int
   *   The value delta.
   */
  public function getDelta() {
    return $this->delta;
  }

}
