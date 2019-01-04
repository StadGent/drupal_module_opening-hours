<?php

namespace Drupal\opening_hours\Services;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\opening_hours\Event\FieldBrokenLinkEvent;
use StadGent\Services\OpeningHours\Exception\ServiceNotFoundException;
use StadGent\Services\OpeningHours\Exception\ChannelNotFoundException;
use StadGent\Services\OpeningHours\Service\Channel\ChannelService;
use StadGent\Services\OpeningHours\Service\Service\ServiceService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Service to sync the opening_hours fields with the service/channel data.
 */
final class SyncService implements SyncServiceInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private $entityTypeManager;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  private $entityFieldManager;

  /**
   * The Opening Hours Service service.
   *
   * @var \StadGent\Services\OpeningHours\Service\Service\ServiceService
   */
  private $serviceService;

  /**
   * The Opening Hours Channel Service.
   *
   * @var \StadGent\Services\OpeningHours\Service\Channel\ChannelService
   */
  private $channelService;

  /**
   * The event dispatcher service.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  private $eventDispatcher;

  /**
   * How much time, in seconds, between 2 fields being synced.
   *
   * The API does not allow more then 60 requests per minute.
   *
   * @var int
   */
  private $throttle = 1;

  /**
   * SyncService constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entityFieldManager
   *   The entity field manager.
   * @param \StadGent\Services\OpeningHours\Service\Service\ServiceService $serviceService
   *   The Opening Hours Service service.
   * @param \StadGent\Services\OpeningHours\Service\Channel\ChannelService $channelService
   *   The Opening Hours Channel service.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
   *   The Event dispatcher service.
   */
  public function __construct(
    EntityTypeManagerInterface $entityTypeManager,
    EntityFieldManagerInterface $entityFieldManager,
    ServiceService $serviceService,
    ChannelService $channelService,
    EventDispatcherInterface $eventDispatcher
  ) {
    $this->entityTypeManager = $entityTypeManager;
    $this->entityFieldManager = $entityFieldManager;
    $this->serviceService = $serviceService;
    $this->channelService = $channelService;
    $this->eventDispatcher = $eventDispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public function syncAll() {
    $count = 0;

    $entityTypes = $this->getEntityTypes();
    foreach ($entityTypes as $entityType) {
      $count += $this->syncEntityType($entityType);
    }

    return $count;
  }

  /**
   * {@inheritdoc}
   */
  public function syncEntityType($entityType) {
    $fields = $this->getEntityTypeFields($entityType);
    $entityIds = $this->getEntityIdsbyFieldNames($entityType, $fields);
    return $this->syncEntities($entityType, $entityIds);
  }

  /**
   * {@inheritdoc}
   */
  public function syncEntities($entityType, array $entityIds) {
    $storage = $this->getEntityTypeStorage($entityType);
    if (!$storage) {
      return 0;
    }

    // Update the entities.
    $count = 0;
    foreach ($entityIds as $entityId) {
      $entity = $storage->load($entityId);
      $count += $this->syncEntity($entity);
    }

    return $count;
  }

  /**
   * {@inheritdoc}
   */
  public function syncEntity(ContentEntityInterface $entity) {
    $count = 0;

    $fields = $this->getEntityTypeBundleFields(
      $entity->getEntityTypeId(),
      $entity->bundle()
    );
    foreach ($fields as $fieldName) {
      $count += $this->syncEntityField($entity, $fieldName);
    }

    if ($count) {
      $this->syncEntitySave($entity);
    }

    return $count;
  }

  /**
   * {@inheritdoc}
   */
  public function syncEntityField(ContentEntityInterface $entity, $fieldName) {
    if (!$entity->hasField($fieldName)) {
      return 0;
    }

    $count = 0;
    $values = $entity->get($fieldName)->getValue();
    foreach ($values as $delta => &$value) {
      if (empty($value['service'])) {
        continue;
      }

      $this->syncEntityFieldValue($entity, $fieldName, $delta, $value);
      $count++;

      $this->throttle();
    }

    $entity->set($fieldName, $values);
    return $count;
  }

  /**
   * Synchronize a single entity field value.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity to synchronize the value for.
   * @param string $fieldName
   *   The field name being synchronized.
   * @param int $delta
   *   The value delta.
   * @param array $value
   *   The value being synchronized.
   */
  protected function syncEntityFieldValue(ContentEntityInterface $entity, $fieldName, $delta, array &$value) {
    $service = $this->loadService($value['service']);
    $channel = $this->loadChannel($service, $value['channel']);

    if ($service) {
      $value['service_label'] = $service->getLabel();
    }
    if ($channel) {
      $value['channel_label'] = $channel->getLabel();
    }

    $value['broken'] = (int) (!$service || !$channel);
    if ($value['broken']) {
      $this->fieldLinkIsBroken($entity, $fieldName, $delta);
    }
  }

  /**
   * Save a synchronized entity.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity to save.
   */
  protected function syncEntitySave(ContentEntityInterface $entity) {
    if ($entity instanceof RevisionableInterface) {
      $entity->setNewRevision(FALSE);
    }

    $entity->save();
  }

  /**
   * Get all the opening hours field map.
   *
   * @return array
   *   The field map for all Opening hours fields.
   */
  protected function getOpeningHoursFieldMaps() {
    return $this
      ->entityFieldManager
      ->getFieldMapByFieldType('opening_hours');
  }

  /**
   * Get the opening hours field map for the given entity type.
   *
   * @param string $entityType
   *   The entity type to get the field map for.
   *
   * @return array
   *   The entity type field map.
   */
  protected function getEntityTypeFieldMap($entityType) {
    $fieldMap = $this->getOpeningHoursFieldMaps();
    if (!isset($fieldMap[$entityType])) {
      return [];
    }

    return $fieldMap[$entityType];
  }

  /**
   * Get all entity types that have opening hours.
   *
   * @return array
   *   Array of entity type names.
   */
  protected function getEntityTypes() {
    return array_keys($this->getOpeningHoursFieldMaps());
  }

  /**
   * Get the opening hours fields for the given entity type.
   *
   * @param string $entityType
   *   The entity type to get the field names for.
   *
   * @return array
   *   Array of field names.
   */
  protected function getEntityTypeFields($entityType) {
    return array_keys($this->getEntityTypeFieldMap($entityType));
  }

  /**
   * Get the opening hours fields for the given entity type and bundle.
   *
   * @param string $entityType
   *   The entity type to get the field names for.
   * @param string $bundle
   *   The entity bundle name to get the field names for.
   *
   * @return array
   *   Array of field names.
   */
  protected function getEntityTypeBundleFields($entityType, $bundle) {
    $fields = [];

    $fieldMap = $this->getEntityTypeFieldMap($entityType);
    foreach ($fieldMap as $fieldName => $info) {
      if (!in_array($bundle, $info['bundles'])) {
        continue;
      }

      $fields[] = $fieldName;
    }

    return $fields;
  }

  /**
   * Get the storage for the given entity type.
   *
   * @param string $entityType
   *   The entity type to get the storage for.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface|null
   *   The entity storage (if any).
   */
  protected function getEntityTypeStorage($entityType) {
    try {
      return $this->entityTypeManager->getStorage($entityType);
    }
    catch (\Exception $e) {
      return NULL;
    }
  }

  /**
   * Get all the entity ids that have the given field names.
   *
   * @param string $entityType
   *   The entity type to get the entity ids for.
   * @param array $fieldNames
   *   Array of field names to get the ids for.
   *
   * @return array
   *   Array of entity ids.
   */
  protected function getEntityIdsbyFieldNames($entityType, array $fieldNames) {
    $storage = $this->getEntityTypeStorage($entityType);

    $query = $storage->getQuery('OR');
    foreach ($fieldNames as $fieldName) {
      $query->exists($fieldName . '.service');
    }

    return $query->execute();
  }

  /**
   * Get the Opening Hours service by its id.
   *
   * @param int $serviceId
   *   The service id to load.
   *
   * @return \StadGent\Services\OpeningHours\Value\Service|null
   *   The loaded service (if any).
   */
  protected function loadService($serviceId) {
    try {
      return $this
        ->serviceService
        ->getById($serviceId);
    }
    catch (ServiceNotFoundException $exception) {
      return NULL;
    }
  }

  /**
   * Get the Opening Hours channel by its id.
   *
   * @param \StadGent\Services\OpeningHours\Value\Service|null $service
   *   The service of the channel.
   * @param int $channelId
   *   The channel ID.
   *
   * @return \StadGent\Services\OpeningHours\Value\Channel|null
   *   The loaded channel (if any).
   */
  protected function loadChannel($service, $channelId) {
    if (!$service) {
      return NULL;
    }

    try {
      return $this
        ->channelService
        ->getById($service->getId(), $channelId);
    }
    catch (ChannelNotFoundException $exception) {
      return NULL;
    }
  }

  /**
   * Method to throttle the number of updates per/second.
   *
   * We need to add some time between API calls due to the rate limit.
   * The default rate limit is 60 requests per minute.
   */
  protected function throttle() {
    sleep($this->throttle);
  }

  /**
   * Dispatch an event when the field link is broken.
   */
  protected function fieldLinkIsBroken(ContentEntityInterface $entity, $fieldName, $delta) {
    $this
      ->eventDispatcher
      ->dispatch(
        FieldBrokenLinkEvent::NAME,
        new FieldBrokenLinkEvent($entity, $fieldName, $delta)
      );
  }

}
