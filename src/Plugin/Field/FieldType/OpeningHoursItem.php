<?php

declare(strict_types=1);

namespace Drupal\opening_hours\Plugin\Field\FieldType;

use Drupal\Core\Field\Attribute\FieldType;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Provides a field type "Opening Hours".
 */
#[FieldType(
  id: 'opening_hours',
  label: new TranslatableMarkup('Opening hours'),
  description: new TranslatableMarkup('Adds a field to select the Service and its Channel to show its opening hours for.'),
  category: 'opening_hours',
  default_widget: 'opening_hours',
  default_formatter: 'opening_hours_widget',
  column_groups: [
    'service' => [
      'label' => new TranslatableMarkup('Service'),
      'translatable' => TRUE,
    ],
    'service_label' => [
       'label' => new TranslatableMarkup('Service label'),
      'translatable' => TRUE,
    ],
    'channel' => [
      'label' => new TranslatableMarkup('Channel'),
      'translatable' => TRUE,
    ],
    'channel_label' => [
      'label' => new TranslatableMarkup('Channel label'),
      'translatable' => TRUE,
    ],
    'broken' => [
      'label' => new TranslatableMarkup('Broken'),
      'translatable' => FALSE,
    ],
  ],
)]
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
    $properties['service'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Service'))
      ->setDescription(new TranslatableMarkup('The service record ID.'));
    $properties['service_label'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Service label'))
      ->setDescription(new TranslatableMarkup('The service label.'));
    $properties['channel'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Channel'))
      ->setDescription(new TranslatableMarkup('The channel record ID.'));
    $properties['channel_label'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Channel label'))
      ->setDescription(new TranslatableMarkup('The channel label.'));
    $properties['broken'] = DataDefinition::create('integer')
      ->setLabel(new TranslatableMarkup('Broken'))
      ->setDescription(new TranslatableMarkup('Indicates if the service/channel link no longer exists in the Opening Hours platform.'));
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function getServiceId(): ?int {
    $serviceId = (int) $this->get('service')->getString();
    return $serviceId ?: NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getServiceLabel(): ?string {
    return $this->get('service_label')->getString();
  }

  /**
   * {@inheritdoc}
   */
  public function getChannelId(): ?int {
    $channelId = (int) $this->get('channel')->getString();
    return $channelId ?: NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getChannelLabel(): ?string {
    return $this->get('channel_label')->getString();
  }

  /**
   * {@inheritdoc}
   */
  public function isBroken(): bool {
    return (bool) (int) $this->get('broken')->getString();
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty(): bool {
    return $this->getServiceId() === NULL
      || $this->getChannelId() === NULL;
  }

}
