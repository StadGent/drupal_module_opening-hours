<?php

namespace Drupal\opening_hours\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Provides a field type "Entrance Fee".
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
 *     "channel" = {
 *       "label" = @Translation("Channel"),
 *       "translatable" = TRUE
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
        'channel' => [
          'description' => 'The channel record ID.',
          'type' => 'int',
          'unsigned' => TRUE,
          'not null' => FALSE,
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
    $properties['channel'] = DataDefinition::create('string');
    return $properties;
  }

}
