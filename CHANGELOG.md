# Changelog

All Notable changes to `drupal/opening-hours` module.

[Unreleased]

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

[8.x-1.0-alpha8]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0-alpha7...8.x-1.0-alpha8
[8.x-1.0-alpha7]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0-alpha6...8.x-1.0-alpha7
[8.x-1.0-alpha6]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0-alpha5...8.x-1.0-alpha6
[8.x-1.0-alpha5]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0-alpha4...8.x-1.0-alpha5
[8.x-1.0-alpha4]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0-alpha3...8.x-1.0-alpha4
[8.x-1.0-alpha3]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0-alpha2...8.x-1.0-alpha3
[8.x-1.0-alpha2]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0-alpha1...8.x-1.0-alpha2
[8.x-1.0-alpha1]: https://github.com/StadGent/drupal_module_opening-hours/releases/tag/8.x-1.0-alpha1
[Unreleased]: https://github.com/StadGent/drupal_module_opening-hours/compare/master...develop
