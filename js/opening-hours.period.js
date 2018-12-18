/**
 * @file
 * Opening hours functions.
 */

'use strict';

/**
 * OpeningHours period containing optional from-until dates.
 *
 * @param {null|Date} from
 *   The from date.
 * @param {null|Date} until
 *   The until date.
 *
 * @returns {OpeningHoursPeriod}
 *   The opening hours period object.
 */
function OpeningHoursPeriod(from, until) {
  this.from = from;
  this.until = until;
}

/**
 * Has the OpeningHoursPeriod a from date.
 *
 * @returns {boolean}
 *   Has a from date.
 */
OpeningHoursPeriod.prototype.hasFrom = function () {
  return this.from !== null;
};

/**
 * Has the OpeningHoursPeriod an until date.
 *
 * @returns {boolean}
 *   Has an until date.
 */
OpeningHoursPeriod.prototype.hasUntil = function () {
  return this.until !== null;
};

/**
 * Has the OpeningHoursPeriod a period.
 *
 * @returns {boolean}
 *   Has a period.
 */
OpeningHoursPeriod.prototype.hasPeriod = function () {
  return this.hasFrom() && this.hasUntil();
};

/**
 * Get the period from.
 *
 * @returns {null|Date}
 *   The period from date (if any).
 */
OpeningHoursPeriod.prototype.getFrom = function () {
  return this.from;
};

/**
 * Get the period until.
 *
 * @returns {null|Date}
 *   The period until (if any).
 */
OpeningHoursPeriod.prototype.getUntil = function () {
  return this.until;
};
