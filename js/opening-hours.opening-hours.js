/**
 * @file
 * Opening hours functions.
 */

'use strict';

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

  if (!this.settings.endpoint || !this.settings.endpoint.length) {
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
    item,
    this.constructWidget(item, this)
  );
};

/**
 * Create the proper API request URI.
 *
 * @param {OpeningHoursItem} item
 *   The opening hours item to render the widget for.
 * @param {function} callback
 *   The callback when the request is processed.
 *
 * @returns {string}
 *   The request URI.
 */
OpeningHours.prototype.constructRequest = function (item, callback) {
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
    case 'Left':
    case 'ArrowLeft':
      changeFocus(--currentPosition, 31, 30, 29, 28);
      break;

    case 'Right':
    case 'ArrowRight':
      changeFocus(++currentPosition, 1);
      break;

    case 'Down':
    case 'ArrowDown':
      changeFocus(currentPosition + 7, currentPosition - 4 * 7, currentPosition - 3 * 7);
      break;

    case 'Up':
    case 'ArrowUp':
      changeFocus(currentPosition - 7, currentPosition + 4 * 7, currentPosition + 3 * 7);
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
      .catch(function (error) {
        item.printError(error);
      })
  }
};
