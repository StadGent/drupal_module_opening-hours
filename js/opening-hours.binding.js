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
          'language': settings.openingHours.language
        };
        new OpeningHours(items, options);

        var a = document.querySelectorAll('.openinghours-navigation a');
        for (var y = 0; y < a.length; y++) {
          a[y].addEventListener('click', function (e) {
            e.preventDefault();

            for (var x = 0; x < a.length; x++) {
              a[x].classList.remove('openinghours-active')
            }
            this.classList.add('openinghours-active');

            var type = this.getAttribute('data-widget');
            var widget = getClosest(this, '.openinghours-wrapper').querySelector('.openinghours-widget');
            widget.setAttribute('data-type', type);
            new OpeningHours([widget], options);
          });
        }
      });
    }
  };

})(jQuery, Drupal);
