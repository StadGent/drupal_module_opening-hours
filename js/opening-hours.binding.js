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
      $(document).once('openingHoursWidget').each(function () {
        var items = document.querySelectorAll('.openinghours-widget');
        var options = {
          'endpoint': settings.openingHours.endpoint,
          'language': settings.openingHours.language,
          'error' : function (request) {
            var elem = getClosest(request.element, '.field');
            elem.parentNode.removeChild(elem);
          }
        };
        new OpeningHours(items, options);
      });
    }
  };

})(jQuery, Drupal);
