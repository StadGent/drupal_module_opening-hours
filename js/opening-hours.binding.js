/**
 * @file
 * Behaviour to load and show the Opening Hours api data.
 */

(function ($, Drupal) {
  'use strict';

  /**
   * Attach the Drupal behaviour for the Opening Hours widget.
   *
   * @type {{attach: Drupal.behaviors.openingHoursWidget.attach}}
   */
  Drupal.behaviors.openingHoursWidget = {
    attach: function (context, settings) {
      var self = this;

      $(document).once('openingHoursWidget').each(function () {
        var items = document.querySelectorAll('.openinghours-widget');

        var options = {
          'endpoint': settings.openingHours.endpoint,
          'endpoint_key': settings.openingHours.endpoint_key,
          'language': settings.openingHours.language,
          'error' : function (item) {
            var elem = getClosest(item, '.openinghours-wrapper');
            if (elem && elem.parentNode) {
              elem.parentNode.removeChild(elem);
            }
          }
        };

        new OpeningHours(items, options);

        var navLinks = document.querySelectorAll('.openinghours-navigation a');
        for (var y = 0; y < navLinks.length; y++) {
          navLinks[y].addEventListener('click', self.switchViewMode(options));
        }
      });
    },

    /**
     * Switch viewMode of the widget.
     *
     * @param {object} options
     *   Options to create the OpeningHours widget.
     *
     * @return {Function}
     *   Callback.
     */
    switchViewMode: function (options) {
      return function (e) {
        e.preventDefault();

        var elements = getClosest(this, '[role=tablist]').querySelectorAll('[role=tab]');
        for (var x = 0; x < elements.length; x++) {
          elements[x].setAttribute('aria-selected', 'false');
        }
        this.setAttribute('aria-selected', 'true');

        // Get new widget type and the widget itself.
        var type = this.getAttribute('data-widget');
        var widget = getClosest(this, '.openinghours-wrapper').querySelector('.openinghours.openinghours-widget');

        // Set the new settings.
        widget.setAttribute('data-type', type);

        // Load new opening hours.
        new OpeningHours([widget], options);
      }
    }
  };

})(jQuery, Drupal);
