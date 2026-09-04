<?php

namespace Drupal\opening_hours\Hook;

use Drupal\Core\Field\FieldTypeCategoryManagerInterface;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Adds the Opening Hours icon to the fields overview.
 */
#[Hook('field_type_category_info_alter')]
final readonly class AddIconToFieldsOverviewHooks {

  /**
   * Add the library containing the icon.
   */
  public function __invoke(&$definitions): void {
    $definitions[FieldTypeCategoryManagerInterface::FALLBACK_CATEGORY]['libraries'][] = 'opening_hours/drupal.opening_hours-icon';
  }

}
