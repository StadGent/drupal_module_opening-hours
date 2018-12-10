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


  for (var i = 0; i < items.length; i++) {
    this._current = items[i];
    this.init();
  }
}

/**
 * Initiate the OpeningHours object.
 *
 * @returns {boolean}
 */
OpeningHours.prototype.init = function () {
  // this.print(this._current, '');

  if (!this.settings.endpoint || 0 === this.settings.endpoint.length) {
    this.printError('Please provide an API endpoint.');
    return false;
  }

  if (isNaN(this._current.dataset.service)) {
    this.printError('Please provide a service.');
    return false;
  }

  if (this.settings.requestDate) {
    this._current.dataset.date = this.settings.requestDate;
  }
  else if (typeof this._current.dataset.date === 'undefined') {
    this._current.dataset.date = new Date().toISOString().slice(0,10);
  }

  this.getTitle(this._current.dataset.service, this._current.dataset.channel);
  this.constructRequest(
    this.constructWidget(this._current, this.settings)
  );
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
    dateString = today.toISOString().slice(0, 10);
  }
  if (dateString === 'tomorrow') {
    var tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    dateString = tomorrow.toISOString().slice(0, 10);
  }

  var date = !dateString ? new Date() : new Date(dateString);

  try {
    var iso = date.toISOString();
  } catch (err) {
    date = new Date();
    iso = date.toISOString();
  }

  return iso.slice(0, 10);
};

/**
 * Create the proper API request URI.
 *
 * @returns {string}
 *   The request URI.
 */
OpeningHours.prototype.constructRequest = function (cb) {
  var requestOptions = {
    parameters: {
      date: this._current.dataset.date,
      language: this.settings.language
    }
  };

  switch (this._current.dataset.type) {
    case 'open-now':
      this.ohw.fetchStatus(
          this._current.dataset.service,
          this._current.dataset.channel,
          'html',
          requestOptions
      ).then(cb);
      break;

    case 'day':
      this.ohw.fetchOpeningHoursForDate(
          this._current.dataset.service,
          this._current.dataset.channel,
          'html',
          requestOptions
      ).then(cb);
      break;

    case 'week':
        this.ohw.fetchOpeningHoursForWeek(
            this._current.dataset.service,
            this._current.dataset.channel,
            'html',
            requestOptions
        ).then(cb);
      break;

    case 'month':
        this.ohw.fetchOpeningHoursForMonth(
            this._current.dataset.service,
            this._current.dataset.channel,
            'html',
            requestOptions
        ).then(cb);
      break;

    case 'year':
        this.ohw.fetchOpeningHoursForYear(
            this._current.dataset.service,
            this._current.dataset.channel,
            'html',
            requestOptions
        ).then(cb);
      break;

    case 'week-from-now':
    default:
        var until = new Date(this._current.dataset.date);
        until.setDate(until.getDate() + 6);

        this.ohw.fetchOpeningHoursByRange(
            this._current.dataset.date,
            until,
            this._current.dataset.service,
            this._current.dataset.channel,
            'html',
            requestOptions
        ).then(cb);
      break;
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
 * Construct openinghours widget.
 *
 * @param {HTMLElement} element
 *   The DOM element in which the data should be printed
 * @param {HTMLElement} settings
 *   The DOM element in which the data should be printed
 * @return {function}
 *   Callback function for the ajax request. The function receives the data returned from the API
 */
OpeningHours.prototype.constructWidget = function (element, settings) {
  return function(data) {
      OpeningHours.prototype.print(element, data);

      if (element.dataset.type === "month") {
          OpeningHours.prototype.calendarEvents(element, settings);
      }
  }
};

/**
 * Print the HTML response to the page.
 *
 * @param element
 *   The DOM element to print the data to.
 * @param {string} data
 *   The data to print in the element.
 */
OpeningHours.prototype.print = function (element, data) {
  element.innerHTML = data;
  if (!element.hasAttribute('tabindex')) {
    element.setAttribute('tabindex', '-1');
  }

  // Dispatch change event
  var evt = document.createEvent('CustomEvent');
  evt.initCustomEvent('change', true, false, { });
  element.dispatchEvent(evt);
};

/**
 * Print an error to the page.
 *
 * @param {string} message
 *   The error message to print.
 */
OpeningHours.prototype.printError = function (message) {
  var error = '<span class="error">Error: ' + message + '</span>';
  this.print(this._current, error);
};

OpeningHours.prototype.calendarEvents = function (element, settings) {
  var self = this;

  element.querySelector('.openinghours--prev').addEventListener('click', function () {
    var month = new Date(element.dataset.date);
    month.setMonth(month.getMonth() - 1, 5);
    element.dataset.date = self.formattedDate(month);

    new OpeningHours([element], settings);
  });

  element.querySelector('.openinghours--next').addEventListener('click', function () {
    var month = new Date(element.dataset.date);
    month.setMonth(month.getMonth() + 1, 5);
    element.dataset.date = self.formattedDate(month);

    new OpeningHours([element], settings);
  });

  var days = element.querySelectorAll('.openinghours--day:not([aria-hidden])');
  for (let i = 0; i < days.length; i++) {
    days[i].addEventListener('keydown', function(e) {
      self.handleKeyboardInput(e, element);
    });

    days[i].addEventListener('click', function (e) {
      for (let x = 0; x < days.length; x++) {
        days[x].setAttribute('tabindex', -1);
        // IE fix: trigger repaint
        days[x].classList.add('inactive');
      }
      this.setAttribute('tabindex', 0);
      // IE fix: trigger repaint
      this.classList.remove('inactive');
      this.focus();
    });
  }
};

/**
 * Handle keyboard input to move to other dates.
 *
 * @param {Event} e
 *   The keydown event.
 * @param {HTMLElement} elem
 *   Wrapper element which contains the days.
 */
OpeningHours.prototype.handleKeyboardInput = function (e, elem) {
  let keyCode = e.keyCode || e.which;
  let current = e.target;
  let currentPosition = +current.getAttribute('aria-posinset');

  const changeFocus = function () {
    let nextElem;
    let i = 0;

    while (!nextElem && i < arguments.length) {
      nextElem = elem.querySelector('[aria-posinset="' + arguments[i] + '"]');
      i++;
    }

    if (nextElem) {
      e.preventDefault();
      nextElem.click();
    }
  };

  switch (keyCode) {
    case 37: // previous (left arrow)
      changeFocus(--currentPosition, 31, 30, 29, 28);
      break;
    case 38: // up (up arrow)
      changeFocus(currentPosition - 7, currentPosition + 4 * 7, currentPosition + 3 * 7);
      break;
    case 40: // down (down arrow)
      changeFocus(currentPosition + 7, currentPosition - 4 * 7, currentPosition - 3 * 7);
      break;
    case 39: // next (right arrow)
      changeFocus(++currentPosition, 1);
      break;
    case 36: // home
      changeFocus(1);
      break;
    case 35: // end
      changeFocus(31, 30, 29, 28);
      break;
  }
};

OpeningHours.prototype.getTitle = function (service, channel) {
  var titleElem = document.querySelector('.openinghours-channel-title[data-service="' + service + '"][data-channel="' + channel + '"]');
  if (titleElem && titleElem.innerHTML === '') {
    this.ohw.fetchChannel(service, channel, 'json')
        .then(function(data) {
          titleElem.innerHTML = data.label
        })
  }
};
