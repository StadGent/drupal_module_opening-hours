<?php

namespace Drupal\opening_hours\Plugin\Field\FieldFormatter;

use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Contains the different Opening Hours widget Types.
 */
class WidgetTypes {
  /**
   * Open Now type.
   *
   * @var string
   */
  const OPEN_NOW = 'opennow';

  /**
   * Day type, will show the overview for a given single date.
   *
   * @var string
   */
  const DAY = 'day';

  /**
   * Week type, this is for the week overview starting on monday.
   *
   * @var string
   */
  const WEEK = 'week';

  /**
   * Week starting today type, this is for the week overview starting today.
   *
   * @var string
   */
  const WEEK_FROM_NOW = 'week_from_now';

  /**
   * Month overview type, will show the current month.
   *
   * @var string
   */
  const MONTH = 'month';

  /**
   * Get a list of available social link types.
   *
   * @return array
   *   Labels keyed by their type.
   */
  public function getList() {
    return [
      self::OPEN_NOW => new TranslatableMarkup('Open now'),
      self::DAY => new TranslatableMarkup('Day'),
      self::WEEK => new TranslatableMarkup('Week'),
      self::WEEK_FROM_NOW => new TranslatableMarkup('Week from now'),
      self::MONTH => new TranslatableMarkup('Month'),
    ];
  }

  /**
   * Get the label for a given type.
   *
   * @param string $type
   *   The type to get the label for.
   *
   * @return string|null
   *   The label (if type is known) or NULL.
   */
  public function getLabelByType($type) {
    $types = $this->getList();
    return isset($types[$type])
      ? $types[$type]
      : NULL;
  }

}
