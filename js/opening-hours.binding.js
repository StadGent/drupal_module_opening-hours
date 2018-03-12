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
        var switchViewMode = function (e) {
          e.preventDefault();

          var elements = getClosest(this, '.openinghours-navigation').querySelectorAll('a');
          for (var x = 0; x < elements.length; x++) {
            elements[x].classList.remove('openinghours-active')
          }
          this.classList.add('openinghours-active');

          // Get new widget type and the widget itself.
          var type = this.getAttribute('data-widget');
          var widget = getClosest(this, '.openinghours-wrapper').querySelector('.openinghours-widget');

          // Always forced switch to today on view mode switch.
          var today = new Date();
          today = today.toISOString().slice(0, 10);

          // Set the new settings.
          widget.setAttribute('data-date', today);
          widget.setAttribute('data-type', type);

          // Load new opening hours.
          new OpeningHours([widget], options);
        };

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

        var a = document.querySelectorAll('.openinghours-navigation a');
        for (var y = 0; y < a.length; y++) {
          a[y].addEventListener('click', switchViewMode);
        }
      });
    }
  };

})(jQuery, Drupal);
