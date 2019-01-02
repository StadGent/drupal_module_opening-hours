<?php

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
 *     "service_name" = {
 *       "label" = @Translation("Service name"),
 *       "translatable" = TRUE
 *     },
 *     "channel" = {
 *       "label" = @Translation("Channel"),
 *       "translatable" = TRUE
 *     },
 *     "channel_name" = {
 *       "label" = @Translation("Channel name"),
 *       "translatable" = TRUE
 *     },
 *     "broken" = {
 *       "label" = @Translation("Broken"),
 *       "translatable" = FALSE
 *     },
 *   },
 * )
 */
class OpeningHoursItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'service' => [
          'description' => 'The service record ID.',
          'type' => 'int',
          'unsigned' => TRUE,
          'not null' => FALSE,
        ],
        'service_name' => [
          'description' => 'The service name.',
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
        'channel_name' => [
          'description' => 'The channel name.',
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
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = [];
    $properties['service'] = DataDefinition::create('string');
    $properties['service_name'] = DataDefinition::create('string');
    $properties['channel'] = DataDefinition::create('string');
    $properties['channel_name'] = DataDefinition::create('string');
    $properties['broken'] = DataDefinition::create('integer');
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    // TODO: empty when broken?
    $service = $this->get('service')->getValue();
    $channel = $this->get('channel')->getValue();

    return empty($service) || empty($channel);
  }

}
