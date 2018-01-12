/**
 * @file
 * Behaviour to load and show the Opening Hours api data.
 */

(function ($, Drupal) {
  'use strict';

  /**
   * Get the closest matching element up the DOM tree.
   * @private
   * @param  {Element} elem     Starting element
   * @param  {String}  selector Selector to match against
   * @return {Boolean|Element}  Returns null if not match found
   */
  var getClosest = function ( elem, selector ) {

    // Element.matches() polyfill
    if (!Element.prototype.matches) {
      Element.prototype.matches =
        Element.prototype.matchesSelector ||
        Element.prototype.mozMatchesSelector ||
        Element.prototype.msMatchesSelector ||
        Element.prototype.oMatchesSelector ||
        Element.prototype.webkitMatchesSelector ||
        function(s) {
          var matches = (this.document || this.ownerDocument).querySelectorAll(s),
            i = matches.length;
          while (--i >= 0 && matches.item(i) !== this) {}
          return i > -1;
        };
    }

    // Get closest match
    for ( ; elem && elem !== document; elem = elem.parentNode ) {
      if ( elem.matches( selector ) ) return elem;
    }

    return null;

  };

  /**
   * Attach the Drupal behaviour for the Opening Hours widget.
   *
   * @type {{attach: Drupal.behaviors.openingHoursWidget.attach}}
   */
  Drupal.behaviors.openingHoursWidget = {
    attach: function (context, settings) {
      var items = document.querySelectorAll('.openinghours-widget');
      var options = {
        'endpoint': settings.openingHours.endpoint,
        'language': settings.openingHours.language
      };
      new OpeningHours(items, options);

      var inactiveItems = document.querySelectorAll('.openinghours-widget:not(.openinghours-active)');
      for (var i = 0; i < inactiveItems.length; i ++) {
        inactiveItems[i].style.display = 'none';
      }

      var a = document.querySelectorAll('.openinghours-navigation a');
      for (var y = 0; y < a.length; y++) {
        a[y].addEventListener('click', function (e) {
          e.preventDefault();

          for (var x = 0; x < a.length; x++) {
            a[x].classList.remove('openinghours-active')
          }
          this.classList.add('openinghours-active');

          for (var i = 0; i < items.length; i ++) {
            items[i].style.display = 'none';
          }

          getClosest(this, '.openinghours-wrapper').querySelector('.openinghours-widget[data-type="' + this.dataset.widget + '"]').style.display = '';
        });
      }
    }
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
    this.items = items;

    for (var i = 0; i < items.length; i++) {
      this._current = items[i];
      this.init(items[i]);
    }
  }

  /**
   * Initiate the OpeningHours object.
   *
   * @returns {boolean}
   */
  OpeningHours.prototype.init = function () {
    if (!this.settings.endpoint || 0 === this.settings.endpoint.length) {
      this.printError('Please provide an API endpoint.');
      return false;
    }

    if (isNaN(this._current.dataset.service) || isNaN(this._current.dataset.channel)) {
      this.printError('Please provide a service and channel.');
      return false;
    }

    if (this.settings.requestDate) {
      this._current.dataset.date = this.settings.requestDate;
    }
    else if (typeof this._current.dataset.date === 'undefined') {
      this._current.dataset.date = new Date().toISOString().slice(0,10);
    }

    if (typeof this._current.dataset.language === 'undefined') {
      this._current.dataset.language = this.settings.language;
    }

    var url = this.constructRequest();
    this.request(url, this.print);
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

    return date.toISOString().slice(0, 10);
  };

  /**
   * Create the proper API request URI.
   *
   * @returns {string}
   *   The request URI.
   */
  OpeningHours.prototype.constructRequest = function () {
    var uri = this.settings.endpoint
        + 'services/'
        + this._current.dataset.service
        + '/channels/'
        + this._current.dataset.channel;

    switch (this._current.dataset.type) {
      case 'open-now':
        return uri + '/open-now';

      case 'day':
        return uri + '/openinghours/day?date=' + this.formattedDate(this._current.dataset.date);

      case 'week':
        return uri + '/openinghours/week?date=' + this.formattedDate(this._current.dataset.date);

      case 'month':
        return uri + '/openinghours/month?date=' + this.formattedDate(this._current.dataset.date);

      case 'year':
        return uri + '/openinghours/year?date=' + this.formattedDate(this._current.dataset.date);

      case 'week-from-now':
      default:
        var until = new Date(this._current.dataset.date);
        until.setDate(until.getDate() + 6);
        return uri + '/openinghours?from=' + this.formattedDate(this._current.dataset.date) + '&until=' + this.formattedDate(until);
    }
  };

  /**
   * Send out the request to the Opening Hours API.
   *
   * @param {string} url
   *   The request URI.
   * @param callback
   *   The callback to pass the response to.
   */
  OpeningHours.prototype.request = function (url, callback) {
    var xmlhttp;
    xmlhttp = new XMLHttpRequest();
    xmlhttp.element = this._current;
    xmlhttp.settings = this.settings;
    xmlhttp.onreadystatechange = function () {
      if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
        callback(xmlhttp, xmlhttp.responseText);
      }
    };
    xmlhttp.open('GET', url, true);
    xmlhttp.setRequestHeader('Accept', 'text/html');
    xmlhttp.setRequestHeader('Accept-Language', this._current.dataset.language);
    xmlhttp.send();
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
   * Print the HTML response to the page.
   *
   * @param element
   *   The DOM element to print the data to.
   * @param {string} data
   *   The data to print in the element.
   */
  OpeningHours.prototype.print = function (obj, data) {
    obj.element.innerHTML = data;

    if (obj.element.dataset.type === "month") {
      OpeningHours.prototype.calendarEvents(obj);
    }
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

  OpeningHours.prototype.calendarEvents = function (obj) {
    var element = obj.element;
    var self = this;

    element.querySelector('.openinghours--month .openinghours--prev').addEventListener('click', function() {
      var month = new Date(element.dataset.date);
      month.setMonth(month.getMonth() - 1);
      element.dataset.date = self.formattedDate(month);
      new OpeningHours([element], obj.settings);
    });
    element.querySelector('.openinghours--month .openinghours--next').addEventListener('click', function() {
      var month = new Date(element.dataset.date);
      month.setMonth(month.getMonth() + 1);
      element.dataset.date = self.formattedDate(month);
      new OpeningHours([element], obj.settings);
    });

    var days = element.querySelectorAll('.openinghours--month .openinghours--day:not([aria-hidden])');
    for (var i = 0; i < days.length; i++) {
      days[i].addEventListener('focus', function (e) {
        for (var x = 0; x < days.length; x++) {
          days[x].setAttribute('tabindex', -1);
        }
        this.setAttribute('tabindex', 0);
      });
    }
  };

})(jQuery, Drupal);
