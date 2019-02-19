<?php

namespace Drupal\opening_hours\Services;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Interface for the Sync service.
 */
interface SyncServiceInterface {

  /**
   * Sync all opening hours fields of all entities.
   *
   * @return int
   *   The number of fields that have been synced.
   */
  public function syncAll();

  /**
   * Sync all opening hours fields for a given entity type.
   *
   * @param string $entityType
   *   The entity type to sync.
   *
   * @return int
   *   The number of fields that have been synced.
   */
  public function syncEntityType($entityType);

  /**
   * Sync all opening hours fields for a given entity type and record ids.
   *
   * @param string $entityType
   *   The entity type to sync.
   * @param array $entityIds
   *   Array of entity ids to sync.
   *
   * @return int
   *   The number of fields that have been synced.
   */
  public function syncEntities($entityType, array $entityIds);

  /**
   * Sync all opening hours fields for a given entity type.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity to update.
   *
   * @return int
   *   The number of fields that have been synced.
   */
  public function syncEntity(ContentEntityInterface $entity);

  /**
   * Sync a single opening hours field of the given entity.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity to sync the opening hours field for.
   * @param string $fieldName
   *   The Opening Hours field name to sync.
   *
   * @return int
   *   The number of opening hours values that have been synced.
   */
  public function syncEntityField(ContentEntityInterface $entity, $fieldName);

}
