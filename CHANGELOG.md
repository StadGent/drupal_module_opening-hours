# Changelog

All Notable changes to `drupal/opening-hours` module.

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

[8.x-1.0-alpha4]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0-alpha3...8.x-1.0-alpha4
[8.x-1.0-alpha3]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0-alpha2...8.x-1.0-alpha3
[8.x-1.0-alpha2]: https://github.com/StadGent/drupal_module_opening-hours/compare/8.x-1.0-alpha1...8.x-1.0-alpha2
[8.x-1.0-alpha1]: https://github.com/StadGent/drupal_module_opening-hours/releases/tag/8.x-1.0-alpha1
[Unreleased]: https://github.com/StadGent/drupal_module_opening-hours/compare/master...develop
