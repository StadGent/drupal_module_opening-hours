<?php

namespace Drupal\opening_hours\Commands;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\opening_hours\Services\SyncService;
use Drush\Commands\DrushCommands;

/**
 * Sync drush command.
 */
final class SyncCommands extends DrushCommands {

  /**
   * Sync service.
   *
   * @var \Drupal\opening_hours\Services\SyncService
   */
  private $syncService;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private $entityTypeManager;

  /**
   * Constructor.
   *
   * @param \Drupal\opening_hours\Services\SyncService $syncService
   *   The sync service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(
    SyncService $syncService,
    EntityTypeManagerInterface $entityTypeManager
  ) {
    parent::__construct();

    $this->syncService = $syncService;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Synchronize all opening hours fields.
   *
   * @param string $what
   *   What should be synchronized.
   *   The options are:
   *   - all : Synchronize all opening hours fields.
   *   - node, term: Synchronize the given entity type
   *     (only content entities are supported)
   *   - node:article : Synchronize alle entities of the given type and bundle.
   *   - node:1 : Synchronize a single entity by its type and id.
   *
   * @command opening-hours:sync
   *
   * @usage drush opening-hours:sync all
   *   Synchronize all opening hours fields.
   * @usage drush opening-hours:sync node
   *   Synchronize all opening hours fields for the given entity type.
   * @usage drush opening-hours:sync node:1
   *   Synchronize all opening hours fields for the given entity type and id.
   *
   * @validate-module-enabled opening_hours
   * @aliases ohs
   */
  public function sync($what) {
    if ($what === 'all') {
      return $this->syncAll();
    }

    if (preg_match('/^[a-z_]+$/', $what)) {
      return $this->syncEntityType($what);
    }

    if (preg_match('/^[a-z_]+:[\d]+$/', $what)) {
      return $this->syncEntity($what);
    }

    $message = sprintf('Provided what "%s" is not in a supported format.', $what);
    $this->writeError($message);
  }

  /**
   * Sync all fields of all entity types.
   */
  protected function syncAll() {
    $message = 'Synchronizing all opening hours fields for all entity types...';
    $this->writeWhat($message);

    $result = $this->syncService->syncAll();
    $this->writeResult($result);
  }

  /**
   * Sync all fields of all entities of the given entity type.
   *
   * @param string $what
   *   The entity type to syncronize.
   */
  protected function syncEntityType($what) {
    $message = sprintf(
      'Synchronizing all opening hours fields for entity type %s...',
      $what
    );
    $this->writeWhat($message);

    $result = $this->syncService->syncEntityType($what);
    $this->writeResult($result);
  }

  /**
   * Sync a single entity.
   *
   * @param string $what
   *   The type:id string.
   */
  protected function syncEntity($what) {
    list($entityType, $entityId) = explode(':', $what, 2);

    $storage = $this->entityTypeManager->getStorage($entityType);
    $entity = $storage->load($entityId);
    if (!$entity) {
      $message = sprintf('No entity of type %s with id %d', $entityType, $entityId);
      $this->writeError($message);
      return;
    }

    $message = sprintf(
      'Synchronizing all opening hours fields of %s %s (%d)...',
      $entity->label(),
      $entityType,
      $entityId
    );
    $this->writeWhat($message);

    $result = $this->syncService->syncEntity($entity);
    $this->writeResult($result);
  }

  /**
   * Print what shall be synchronized.
   *
   * @param string $message
   *   The message to print.
   */
  protected function writeWhat($message) {
    $formattedLine = sprintf('<comment>%s</comment>', $message);
    $this->writeln($formattedLine);
  }

  /**
   * Print the result.
   *
   * @param int $count
   *   The number of synchronized fields values.
   */
  protected function writeResult($count) {
    $section = $count > 0
      ? 'info'
      : 'comment';

    $message = $count === 1
      ? 'Processed 1 field.'
      : sprintf('Processed %d fields.', $count);

    $formattedLine = sprintf('<%s>Done</%s> : %s', $section, $section, $message);
    $this->say($formattedLine);
  }

  /**
   * Print an error message.
   *
   * @param string $message
   *   The message to print.
   */
  protected function writeError($message) {
    $formattedLine = sprintf('<error> %s </error>', $message);
    $this->writeln($formattedLine);
  }

}
