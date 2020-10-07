<?php

namespace Drupal\opening_hours\Plugin\Field\FieldType;

/**
 * The Opening Hours item interface.
 */
interface OpeningHoursItemInterface {

  /**
   * Get the Service label.
   *
   * @return string|null
   *   The label (if any).
   */
  public function getServiceLabel(): ?string;

  /**
   * Get the Service ID.
   *
   * @return int|null
   *   The id (if any).
   */
  public function getServiceId(): ?int;

  /**
   * Get the Channel label.
   *
   * @return string|null
   *   The label (if any).
   */
  public function getChannelLabel(): ?string;

  /**
   * Get the Channel ID.
   *
   * @return int|null
   *   The id (if any).
   */
  public function getChannelId(): ?int;

  /**
   * Is the link broken with the opening hours service.
   *
   * @return bool
   *   Is broken.
   */
  public function isBroken(): bool;

}
