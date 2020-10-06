<?php

declare(strict_types=1);

namespace Drupal\opening_hours\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Provides a field type "Opening Hours".
 *
 * @FieldType(
 *   id = "opening_hours",
 *   label = @Translation("Opening Hours"),
 *   description = @Translation("Adds a field to select the Service and its Channel to show its opening hours for."),
 *   category = @Translation("Web services"),
 *   module = "opening_hours",
 *   default_formatter = "opening_hours_widget",
 *   default_widget = "opening_hours",
 *   column_groups = {
 *     "service" = {
 *       "label" = @Translation("Service"),
 *       "translatable" = TRUE
 *     },
 *     "service_label" = {
 *       "label" = @Translation("Service label"),
 *       "translatable" = TRUE
 *     },
 *     "channel" = {
 *       "label" = @Translation("Channel"),
 *       "translatable" = TRUE
 *     },
 *     "channel_label" = {
 *       "label" = @Translation("Channel label"),
 *       "translatable" = TRUE
 *     },
 *     "broken" = {
 *       "label" = @Translation("Broken"),
 *       "translatable" = FALSE
 *     },
 *   },
 * )
 */
class OpeningHoursItem extends FieldItemBase implements OpeningHoursItemInterface {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    return [
      'columns' => [
        'service' => [
          'description' => 'The service record ID.',
          'type' => 'int',
          'unsigned' => TRUE,
          'not null' => FALSE,
        ],
        'service_label' => [
          'description' => 'The service label.',
          'type' => 'varchar',
          'length' => 255,
          'not null' => FALSE,
        ],
        'channel' => [
          'description' => 'The channel record ID.',
          'type' => 'int',
          'unsigned' => TRUE,
          'not null' => FALSE,
        ],
        'channel_label' => [
          'description' => 'The channel label.',
          'type' => 'varchar',
          'length' => 255,
          'not null' => FALSE,
        ],
        'broken' => [
          'description' => 'Indicates if the service/channel link no longer exists in the Opening Hours platform.',
          'type' => 'int',
          'size' => 'tiny',
          'unsigned' => TRUE,
          'not null' => TRUE,
          'default' => 0,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    $properties = [];
    $properties['service'] = DataDefinition::create('string');
    $properties['service_label'] = DataDefinition::create('string');
    $properties['channel'] = DataDefinition::create('string');
    $properties['channel_label'] = DataDefinition::create('string');
    $properties['broken'] = DataDefinition::create('integer');
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function getServiceId(): ?int {
    return $this->get('service')->getValue() ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getServiceLabel(): ?string {
    return $this->get('service_label')->getValue() ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getChannelId(): ?int {
    return $this->get('channel')->getValue() ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getChannelLabel(): ?string {
    return $this->get('channel_label')->getValue() ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function isBroken(): bool {
    return (bool) (int) $this->get('broken')->getValue();
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty(): bool {
    return empty($this->getServiceId())
      || empty($this->getChannelId());
  }

}
