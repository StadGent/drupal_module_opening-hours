<?php

namespace Drupal\opening_hours\Plugin\views\filter;

use Drupal\views\Plugin\views\filter\BooleanOperator;

/**
 * Filters by given list of node title options.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("opening_hours_broken")
 */
class Broken extends BooleanOperator {

  /**
   * Run the query to get all entities by their broken link status.
   */
  public function query() {
    $this->ensureMyTable();
    $fieldName = sprintf('%s.%s', $this->tableAlias, $this->realField);

    $where = empty($this->value)
      ? sprintf('(%s = 0 OR %s IS NULL)', $fieldName, $fieldName)
      : sprintf('%s <> 0', $fieldName);

    /** @var \Drupal\views\Plugin\views\query\Sql $query */
    $query = $this->query();
    $query->addWhereExpression($this->options['group'], $where);
  }

}
