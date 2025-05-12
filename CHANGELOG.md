# Changelog

All Notable changes to `drupal/opening-hours` module.

## [Unreleased]

### Added

* DMOH-60: Add Drupal 11 support.
* Add distro to travis to prevent failing tests.

### Changed

* Change travis min php version to 8.3 and 8.4.
* Change travis min drupal version to 10.3 and 11.0.

## [2.2.1]

### Fixed

* VG-2540: Fix views Broken filter.

## [2.2.0]

### Added

* DMOH-59: Add support for Drupal 10.

### Changed

* DMOH-59: Change minimal Drupal version to 9.4 or 10.0.
* DMOH-59: Change minimal PHP version to 8.1.

## [2.1.0]

### Changed

* Change widget tab labels.

## [2.0.0]

### Fixed

- Fix code issues.

### Changed

- Change minimal drupal version to 9.3.
- Change Client service creation to a factory.

### Updated

- Update qa-drupal.
- Update stadgent/services-opening-hours package to 2.x.

## [1.5.0]

### Added

- Add support for PHP 8.x.

## [1.4.1]

### Changed

* Added support for Ajax.

## [1.4.0]

### Changed

* Switched to semantic versioning.

## [8.x-1.3]

### Added

* Added GrumPHP + code quality tools configuration.
* Added TravisCI script to automatically validate the code quality.
* Added the Drupal 9 compatibility check.

### Fixed

* DMOH-57: Fixed broken entity form serialization due to manually injecting the
  Logger instance into the OpeningHoursWidget.
  Is now replaced by `use LoggerChannelTrait;`.

## [8.x-1.2]

### Changed

* When the opening hours can not be retrieved an error message was printed in
  the HTML output. This is now changed: when an error happens, no output is
  printed into the HTML.

### Fixed

* DMOH-56 : Added missing tokens.

## [8.x-1.1]

**IMPORTANT : From now on, an API key is required due to a change in the API
endpoint.**

Change the API endpoint and set the API key value in the webservice
configuration after updating the module to this version.

See [service documentation](https://developer.gent.be/docs/dataset?service_id=openingsuren_service)
for more information about the endpoint.

### Changed

* DMOH-55: Changed the way to access the endpoint: added support to set the
  required API key.

### Fixed

* DMOH-53: Fixed not unique element wrapper.
* DMOH-53: Fixed detecting if the element was submitted.

## [8.x-1.0]

### Added

* VG-1466: Added the service and channel labels to the opening_hours field.
* VG-1466: Added synchronization command that updates all channel and service
  labels and checks if the service/channel combination still exists in the
  backend.
* VG-1519: Added new OpeningHours formatter to display the service & channel
  labels.
* VG-1519: Added new "Broken" views filter for the opening_hours:broken field.

### Fixed

* DMOH-51: Fixed loading the channels once a service is selected from the
  autocomplete suggestions.
* DMOH-52: Fixed supporting usage of the field widget within paragraphs (fixed
  extracting the form submit values for Ajax callbacks).

## [8.x-1.0-beta1]

### Added

* VG-1476: Added support to limit fetching the opening hours with an optional
  from-until period.

### Changed

* SGD8-709: Moved generic javascript code to the
  '@digipolis-gent/opening-hours-widget' npm package.

## [8.x-1.0-alpha16]

### Changed

* DMOH-48: Changed the week/month view swith links to more generic labels.

## [8.x-1.0-alpha15]

### Fixed

* VG-1385: Forced IE to trigger a repaint after focus.

## [8.x-1.0-alpha14]

### Fixed

* VG-1385: Enabled placing the focus on days in IE.

### Removed

* VG-1385: Removed autofocus after init.

## [8.x-1.0-alpha13]

### Added

* VG-1385: Added keyboard support for the month view.

## [8.x-1.0-alpha12]

### Fixed

* Fixed translation header.

## [8.x-1.0-alpha11]

### Added

* Added a preview date widget to the opening hours field formatter.

## [8.x-1.0-alpha10]

### Added

* Added opening hours package version to 1.0 or higher.

## [8.x-1.0-alpha9]

### Fixed

* DMOH-42: Always forced switch to today on view mode switch.

## [8.x-1.0-alpha8]

### Fixed

* DMOH-41: Added isEmpty() function to Opening Hours field type.

## [8.x-1.0-alpha7]

### Fixed

* DMOH-38: Fixed removed opening hours issues.
* DMOH-40: Added language parameter to the url to avoid browser caching.

## [8.x-1.0-alpha6]

### Added

* DMOH-38: Added optional error callback function to OpeningHours.

## [8.x-1.0-alpha5]

### Fixed

* DMOH-36: Fixed Javascript error if date is in an invalid format.
* DMOH-37: Fixed an issue whit the view mode navigation switch.

## [8.x-1.0-alpha4]

### Fixed

* DMOH-34: Fixed a daylight saving time issue.

## [8.x-1.0-alpha3]

### Added

* DMOH-26: Added support to set widget tag data-date as "today" or "tomorrow".
* DMOH-27: Added custom render element (opening_hours_widget) to limit the code
  required to render the widget tag.
* DMOH-29: Added support to set the date to get the opening hours for.
* DMOH-31: Added option to switch between 2 widget types.

### Fixed

* DMOH-32: Removed double .openinghours-widget loop to prevent multiple requests
for the same widget.

### Removed

* DMOH-30: Removed language options from the element.

## [8.x-1.0-alpha2]

### Added

* DMOH-22: Added the opening hours widget formatter.

### Fixed

* Fixed package name.

## [8.x-1.0-alpha1]

### Added

* DMOH-19: Added service configuration form.
* DMOH-20: Added the opening hours field type.
* DMOH-21: Added the opening hours field widget.

[2.2.1]: https://github.com/StadGent/drupal_module_opening-hours/compare/2.2.0...2.2.1
[2.2.0]: https://github.com/StadGent/drupal_module_opening-hours/compare/2.1.0...2.2.0
[2.1.0]: https://github.com/StadGent/drupal_module_opening-hours/compare/2.0.0...2.1.0
[2.0.0]: https://github.com/StadGent/drupal_module_opening-hours/compare/1.5.0...2.0.0
[1.5.0]: https://github.com/StadGent/drupal_module_opening-hours/compare/1.4.1...1.5.0
[1.4.1]: https://github.com/StadGent/drupal_module_opening-hours/compare/1.4.0...1.4.1
[1.4.0]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.3...1.4.0
[8.x-1.3]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.2...8.x-1.3
[8.x-1.2]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.1...8.x-1.2
[8.x-1.1]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0...8.x-1.1
[8.x-1.0]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0-beta1...8.x-1.0
[8.x-1.0-beta1]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0-alpha16...8.x-1.0-beta1
[8.x-1.0-alpha16]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0-alpha15...8.x-1.0-alpha16
[8.x-1.0-alpha15]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0-alpha14...8.x-1.0-alpha15
[8.x-1.0-alpha14]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0-alpha13...8.x-1.0-alpha14
[8.x-1.0-alpha13]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0-alpha12...8.x-1.0-alpha13
[8.x-1.0-alpha12]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0-alpha11...8.x-1.0-alpha12
[8.x-1.0-alpha11]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0-alpha10...8.x-1.0-alpha11
[8.x-1.0-alpha10]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0-alpha9...8.x-1.0-alpha10
[8.x-1.0-alpha9]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0-alpha8...8.x-1.0-alpha9
[8.x-1.0-alpha8]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0-alpha7...8.x-1.0-alpha8
[8.x-1.0-alpha7]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0-alpha6...8.x-1.0-alpha7
[8.x-1.0-alpha6]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0-alpha5...8.x-1.0-alpha6
[8.x-1.0-alpha5]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0-alpha4...8.x-1.0-alpha5
[8.x-1.0-alpha4]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0-alpha3...8.x-1.0-alpha4
[8.x-1.0-alpha3]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0-alpha2...8.x-1.0-alpha3
[8.x-1.0-alpha2]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0-alpha1...8.x-1.0-alpha2
[8.x-1.0-alpha1]: https://github.com/StadGent/drupal_module_opening-hours/releases/tag/8.x-1.0-alpha1
[Unreleased]: https://github.com/StadGent/drupal_module_opening-hours/compare/main...develop
