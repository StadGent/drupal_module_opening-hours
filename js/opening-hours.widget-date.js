/**
 * @file
 * Opening hours functions.
 */

'use strict';

/**
 * The date to create the OpeningHours widget for.
 *
 * The current date is used to generate the widget for (day, week, month)...
 * This object allows manipulating the widget date without losing the original
 * value. It allows to reset the date if necessary.
 *
 * @param {Date} date
 *   The date to create the widget date for.
 *
 * @return {OpeningHoursWidgetDate}
 *   The Openinghours widget date.
 */
function OpeningHoursWidgetDate(date) {
  this.originalDate = date;
  this.date = date;
}

/**
 * Get the orginal date.
 *
 * @returns {Date}
 *   The original date.
 */
OpeningHoursWidgetDate.prototype.getOriginalDate = function () {
  return this.originalDate;
};

/**
 * Get the date to generate the widget for.
 *
 * @return {Date}
 *   The date.
 */
OpeningHoursWidgetDate.prototype.getDate = function () {
  return this.date;
};

/**
 * Reset the widget date to the original value.
 */
OpeningHoursWidgetDate.prototype.reset = function () {
  this.date = this.getOriginalDate();
};

/**
 * Set the widget to the previous week.
 */
OpeningHoursWidgetDate.prototype.previousWeek = function () {
  this.date.setDate(this.date.getDate() - 7);
};

/**
 * Set the widget to the next week.
 */
OpeningHoursWidgetDate.prototype.nextWeek = function () {
  this.date.setDate(this.date.getDate() + 7);
};

/**
 * Set the widget to the previous month.
 */
OpeningHoursWidgetDate.prototype.previousMonth = function () {
  this.date.setMonth(this.date.getMonth() - 1, 1);
};

/**
 * Set the widget date to next month.
 */
OpeningHoursWidgetDate.prototype.nextMonth = function () {
  this.date.setMonth(this.date.getMonth() + 1, 1);
};
