/**
 * @file
 * Opening hours functions.
 */

'use strict';

/**
 * OpeningHours item, is a wrapper around the opening hours widget HTML Element.
 *
 * @param {HTMLElement} element
 *   The dom element to create the item for.
 * @param {object} options
 *   The OpeningHours options.
 *
 * @returns {OpeningHoursItem}
 *   The opening hours item.
 */
function OpeningHoursItem(element, options) {
  this.element = element;
  this.options = options;

  // The widget type.
  this.type = null;

  // The OpeningHours Service & Channel.
  this.service = null;
  this.channel = null;

  // The date the item want's to show the widget for.
  this.date = null;

  // Optional period to limit the opening hours request by.
  this.period = null;
}

/**
 * Get the item widget type.
 *
 * @return {false|string}
 *   The widget type.
 */
OpeningHoursItem.prototype.getType = function () {
  if (this.type !== null) {
    return this.type;
  }

  if (typeof this.element.dataset.type === 'undefined') {
    this.printError('Please provide a widget type.');
    this.type = false;
  }
  else {
    this.type = this.element.dataset.type;
  }

  return this.type;
};

/**
 * Get the item Service ID.
 *
 * @return {false|integer}
 *   The Service id (if any).
 */
OpeningHoursItem.prototype.getService = function () {
  if (this.service !== null) {
    return this.service;
  }

  if (isNaN(this.element.dataset.service)) {
    this.printError('Please provide a service id.');
    this.service = false;
  }
  else {
    this.service = parseInt(this.element.dataset.service);
  }

  return this.service;
};

/**
 * Get the item Channel ID.
 *
 * @return {false|integer}
 *   The channel (if any).
 */
OpeningHoursItem.prototype.getChannel = function () {
  if (this.channel !== null) {
    return this.channel;
  }

  if (isNaN(this.element.dataset.channel)) {
    this.channel = false;
  }
  else {
    this.channel = parseInt(this.element.dataset.channel);
  }

  return this.channel;
};

/**
 * Get the date the item wants to show the widget for.
 *
 * @return {OpeningHoursWidgetDate}
 *   The Opening Hours widget date object.
 */
OpeningHoursItem.prototype.getDate = function () {
  if (this.date !== null) {
    return this.date;
  }

  // If the options request date is set, use that one (oh_date request param).
  if (this.options.requestDate) {
    this.date = new OpeningHoursWidgetDate(new Date(this.options.requestDate));
    return this.date;
  }

  // Fallback to current date if no date is set in the widget data (data-date).
  if (typeof this.element.dataset.date === 'undefined') {
    this.date = new OpeningHoursWidgetDate(new Date());
    return this.date;
  }

  // Date from the data-date property.
  this.date = new OpeningHoursWidgetDate(new Date(this.element.dataset.date));
  return this.date;
};

/**
 * Get the period filter the item wants to limit the opening hours by.
 *
 * @returns {OpeningHoursPeriod}
 *   The period object.
 */
OpeningHoursItem.prototype.getPeriod = function () {
  if (this.period !== null) {
    return this.period;
  }

  var from = typeof this.element.dataset.from !== 'undefined'
    ? new Date(this.element.dataset.from)
    : null;
  var until = typeof this.element.dataset.until !== 'undefined'
    ? new Date(this.element.dataset.until)
    : null;

  this.period = new OpeningHoursPeriod(from, until);
  return this.period;
};

/**
 * Has the widget everything to create a widget of it.
 *
 * @return {boolean}
 *   Everything in place to create a widget out of it.
 */
OpeningHoursItem.prototype.isWidgetable = function () {
  return this.getType() && this.getService();
};

/**
 * Print HTML to the page on the location of the item.
 *
 * @param {string} data
 *   The data to print in the element.
 */
OpeningHoursItem.prototype.print = function (data) {
  this.element.innerHTML = data;
  if (!this.element.hasAttribute('tabindex')) {
    this.element.setAttribute('tabindex', '-1');
  }

  // Dispatch change event.
  var evt = document.createEvent('CustomEvent');
  evt.initCustomEvent('change', true, false, {});
  this.element.dispatchEvent(evt);
};

/**
 * Print an error to the console and optionally execute the error option.
 *
 * @param {string} message
 *   The error message to print.
 */
OpeningHoursItem.prototype.printError = function (message) {
  console.error(message);
  if (this.options.error) {
    this.options.error(this.element);
  }
};
