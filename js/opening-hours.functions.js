/**
 * @file
 * Opening hours functions.
 */

'use strict';

/**
 * Get the closest matching element up the DOM tree.
 *
 * @private
 *
 * @param {Element} elem     Starting element
 * @param {String} selector Selector to match against
 *
 * @return {Boolean|Element}  Returns null if not match found
 */
var getClosest = function (elem, selector) {

  // Element.matches() polyfill.
  if (!Element.prototype.matches) {
    Element.prototype.matches =
      Element.prototype.matchesSelector ||
      Element.prototype.mozMatchesSelector ||
      Element.prototype.msMatchesSelector ||
      Element.prototype.oMatchesSelector ||
      Element.prototype.webkitMatchesSelector ||
      function (s) {
        var matches = (this.document || this.ownerDocument).querySelectorAll(s),
          i = matches.length;
        while (--i >= 0 && matches.item(i) !== this) {
          // ??
        }
        return i > -1;
      };
  }

  // Get closest match.
  for (; elem && elem !== document; elem = elem.parentNode) {
    if (elem.matches(selector)) {
      return elem;
    }
  }

  return null;
};


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
    this.printError('Please provide a channel id.');
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
  return this.getType() !== false
    && this.getService() !== false
    && this.getChannel() !== false;
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
 * Print an error to the page.
 *
 * @param {string} message
 *   The error message to print.
 */
OpeningHoursItem.prototype.printError = function (message) {
  console.error(message);
  var error = '<span class="error">Error: ' + message + '</span>';
  this.print(this.element, error);
};


/**
 * Define the OpeningHours object.
 *
 * @param items
 *   The items to add load the openinghours data for.
 * @param options
 *   The OpeningHours options.
 *
 * @returns {OpeningHours}
 *   The OpeningHours object.
 *
 * @constructor
 */
function OpeningHours(items, options) {
  var defaults = {
    endpoint: '',
    language: 'en',
    requestDate: this.getRequestDateFromUrl()
  };

  // Merge options into default settings.
  Object.keys(options).forEach(function (k) {
    defaults[k] = options[k];
  });

  this.settings = defaults;
  this.ohw = new OpeningHoursWidget({
    endpoint: this.settings.endpoint
  });

  if (!this.settings.endpoint || 0 === this.settings.endpoint.length) {
    console.error('OpeningHours : Please provide an API endpoint.');
    return this;
  }

  // Render the widgets for all found items.
  for (var i = 0; i < items.length; i++) {
    this.renderItemWidget(
      new OpeningHoursItem(items[i], this.settings)
    );
  }
}

/**
 * Format a given date object into the yyyy-mm-dd format.
 *
 * @param {Date} date
 *   The date to format.
 *
 * @returns {string}
 *   The date in the proper format.
 */
OpeningHours.prototype.formatDate = function (date) {
  return date.toISOString().slice(0,10);
};

/**
 * Create a proper date format (yyyy-mm-dd) from string.
 *
 * @param {string} dateString
 *   The data string to parse.
 *
 * @returns {string}
 *   The date in the proper format.
 */
OpeningHours.prototype.formattedDate = function (dateString) {
  if (dateString === 'today') {
    var today = new Date();
    return this.formatDate(today);
  }

  if (dateString === 'tomorrow') {
    var tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    return this.formatDate(tomorrow);
  }

  var date = !dateString ? new Date() : new Date(dateString);

  try {
    return this.formatDate(date);
  }
  catch (err) {
    date = new Date();
    return this.formatDate(date);
  }
};

/**
 * Render the widget for the given item.
 *
 * @param {OpeningHoursItem} item
 *   The item to render.
 */
OpeningHours.prototype.renderItemWidget = function (item) {
  if (!item.isWidgetable()) {
    return false;
  }

  this.getTitle(item);
  this.constructRequest(
    this.constructWidget(item, this),
    item
  );
};

/**
 * Create the proper API request URI.
 *
 * @param {function} callback
 *   The callback when the request is processed.
 * @param {OpeningHoursItem} item
 *   The opening hours item to render the widget for.
 *
 * @returns {string}
 *   The request URI.
 */
OpeningHours.prototype.constructRequest = function (callback, item) {
  switch (item.getType()) {
    case 'open-now':
      this.ohw.fetchStatus(
        item.getService(),
        item.getChannel(),
        'html',
        this.constructRequestOptions(item)
      ).then(callback);
      break;

    case 'day':
      this.ohw.fetchOpeningHoursForDate(
        item.getService(),
        item.getChannel(),
        'html',
        this.constructRequestOptions(item)
      ).then(callback);
      break;

    case 'week':
      this.ohw.fetchOpeningHoursForWeek(
        item.getService(),
        item.getChannel(),
        'html',
        this.constructRequestPeriodOptions(item)
      ).then(callback);
      break;

    case 'month':
      this.ohw.fetchOpeningHoursForMonth(
        item.getService(),
        item.getChannel(),
        'html',
        this.constructRequestPeriodOptions(item)
      ).then(callback);
      break;

    case 'year':
      this.ohw.fetchOpeningHoursForYear(
        item.getService(),
        item.getChannel(),
        'html',
        this.constructRequestPeriodOptions(item)
      ).then(callback);
      break;

    case 'week-from-now':
    default:
      item.getDate().reset();

      // Alter the period start if the data-from is after the data-date.
      var from = item.getDate().getOriginalDate();
      if (item.getPeriod().hasFrom() && item.getPeriod().getFrom() > from) {
        from = item.getPeriod().getFrom();
      }

      // Alter the period end if the data-until is before the end of the week.
      var until = new Date(from.valueOf());
      until.setDate(until.getDate() + 6);
      if (item.getPeriod().hasUntil() && item.getPeriod().getUntil() < until) {
        until = item.getPeriod().getUntil();
      }

      this.ohw.fetchOpeningHoursByRange(
        from,
        until,
        item.getService(),
        item.getChannel(),
        'html',
        this.constructRequestOptions(item)
      ).then(callback);
      break;
  }
};

/**
 * Get the request options for the given item.
 *
 * @param {OpeningHoursItem} item
 *   The opening hours item to get the request options for.
 *
 * @returns {object}
 *   The request options object.
 */
OpeningHours.prototype.constructRequestOptions = function (item) {
  return {
    parameters: {
      date: this.formatDate(item.getDate().getDate()),
      language: this.settings.language
    }
  };
};

/**
 * Get the request options with optional period for the given item.
 *
 * @param {OpeningHoursItem} item
 *   The opening hours item to get the request options for.
 *
 * @returns {object}
 *   The request options object.
 */
OpeningHours.prototype.constructRequestPeriodOptions = function (item) {
  var requestOptions = this.constructRequestOptions(item);

  if (item.getPeriod().hasFrom()) {
    requestOptions.parameters.from = this.formatDate(
      item.getPeriod().getFrom()
    );
  }

  if (item.getPeriod().hasUntil()) {
    requestOptions.parameters.until = this.formatDate(
      item.getPeriod().getUntil()
    );
  }

  return requestOptions;
};

/**
 * Construct openinghours widget.
 *
 * @param {OpeningHoursItem} item
 *   The opening hours item to get the opening hours for.
 * @param {OpeningHours} openingHours
 *   The opening hours object.
 *
 * @returns {function}
 *   Callback function for the ajax request. The function receives the data returned from the API
 */
OpeningHours.prototype.constructWidget = function (item, openingHours) {
  return function (data) {
    item.print(data);

    if (item.getType() === 'month') {
      openingHours.calendarEvents(item);
    }
  }
};

/**
 * Get the data-date value from the URL.
 *
 * @return {string|null}
 *   The date in yyy-mm-dd format.
 */
OpeningHours.prototype.getRequestDateFromUrl = function () {
  var parameterDate = this.findGetParameter('oh_day');
  if (!parameterDate) {
    return null;
  }

  return this.formattedDate(parameterDate);
};

/**
 * Get a GET parameter from the URL.
 *
 * @param {string} key
 *   The GET parameter key.
 *
 * @return {string|null}
 *   The GET value (if any).
 */
OpeningHours.prototype.findGetParameter = function (key) {
  var result = null;
  var tmp = [];
  var items = location.search.substr(1).split("&");

  for (var index = 0; index < items.length; index++) {
    tmp = items[index].split("=");
    if (tmp[0] === key) {
      result = decodeURIComponent(tmp[1]);
    }
  }

  return result;
};

/**
 * Bind the calendar events.
 *
 * @param {OpeningHoursItem} item
 *   The Opening Hours item.
 */
OpeningHours.prototype.calendarEvents = function (item) {
  var element = item.element;
  var openingHours = this;

  element.querySelector('.openinghours--prev').addEventListener('click', function () {
    item.getDate().previousMonth();
    openingHours.renderItemWidget(item);
  });

  element.querySelector('.openinghours--next').addEventListener('click', function () {
    item.getDate().nextMonth();
    openingHours.renderItemWidget(item);
  });

  var days = element.querySelectorAll('.openinghours--day:not([aria-hidden])');
  for (var i = 0; i < days.length; i++) {
    days[i].addEventListener('keydown', function (e) {
      openingHours.handleKeyboardInput(e, element);
    });

    days[i].addEventListener('click', function () {
      for (var x = 0; x < days.length; x++) {
        days[x].setAttribute('tabindex', -1);
        // IE fix: trigger repaint.
        days[x].classList.add('inactive');
      }

      this.setAttribute('tabindex', 0);

      // IE fix: trigger repaint.
      this.classList.remove('inactive');
      this.focus();
    });
  }
};

/**
 * Handle keyboard input to move to other dates.
 *
 * @param {KeyboardEvent} event
 *   The keydown event.
 * @param {HTMLElement} element
 *   Wrapper element which contains the days.
 */
OpeningHours.prototype.handleKeyboardInput = function (event, element) {
  var key = event.key;
  var current = event.target;
  var currentPosition = +current.getAttribute('aria-posinset');

  console.log(key);

  var changeFocus = function () {
    var nextElem;
    var i = 0;

    while (!nextElem && i < arguments.length) {
      nextElem = element.querySelector('[aria-posinset="' + arguments[i] + '"]');
      i++;
    }

    if (nextElem) {
      event.preventDefault();
      nextElem.click();
    }
  };

  switch (key) {
    case 'ArrowLeft':
      changeFocus(--currentPosition, 31, 30, 29, 28);
      break;

    case 'ArrowRight':
      changeFocus(currentPosition - 7, currentPosition + 4 * 7, currentPosition + 3 * 7);
      break;

    case 'ArrowDown':
      changeFocus(currentPosition + 7, currentPosition - 4 * 7, currentPosition - 3 * 7);
      break;

    case 'ArrowUp':
      changeFocus(++currentPosition, 1);
      break;

    case 'Home':
      changeFocus(1);
      break;

    case 'End':
      changeFocus(31, 30, 29, 28);
      break;
  }
};

/**
 * Set the title for the given OpeningHours item.
 *
 * @param {OpeningHoursItem} item
 *   The item to update the title for.
 */
OpeningHours.prototype.getTitle = function (item) {
  var titleElem = document.querySelector(
    '.openinghours-channel-title[data-service="' + item.getService() + '"][data-channel="' + item.getChannel() + '"]'
  );
  if (titleElem && titleElem.innerHTML === '') {
    this
      .ohw
      .fetchChannel(item.getService(), item.getChannel(), 'json')
      .then(function (data) {
        titleElem.innerHTML = data.label
      })
  }
};
