<?php

/**
 * @file
 * Provide views data about opening_hours related data.
 */

use Drupal\field\FieldStorageConfigInterface;

/**
 * Implements hook_field_views_data().
 *
 * The function implements the hook in behalf of 'core' because it adds a
 * relationship and a reverse relationship to entity_reference field type, which
 * is provided by core.
 */
function opening_hours_field_views_data(FieldStorageConfigInterface $field_storage) {
  $data = views_field_default_views_data($field_storage);

  if ($field_storage->getType() !== 'opening_hours') {
    return $data;
  }

  $fieldName = sprintf('%s_broken', $field_storage->getName());
  foreach ($data as $tableName => &$tableData) {
    if (!isset($tableData[$fieldName]['filter'])) {
      continue;
    }

    $tableData[$fieldName]['filter']['id'] = 'opening_hours_broken';
  }

  return $data;
}
