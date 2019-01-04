# Drupal 8 : Opening Hours module

[![License][ico-license]][link-license]

Drupal 8 module that integrates the Opening Hours platform functionality.

This module allows to consume the [Opening Hours Platform] API to lookup
services, their channels and their opening hours data.

## Install

Install using composer:

Add the git source to the composer by editing the `composer.json` in the project
root and add following lines in the `repositories section:

```json
    {
        "type": "package",
        "package": {
            "name": "drupal/opening-hours-widget",
            "version": "0.0.3",
            "type": "drupal-library",
            "dist": {
                "url": "https://github.com/StadGent/npm_package_opening-hours-widget/releases/download/v0.0.3/opening-hours-widget.zip",
                "type": "zip"
            }
        }
    },
    {
        "type": "vcs",
        "url": "git@github.com:StadGent/drupal_module_opening-hours.git"
    }
```

Install the module using composer:

```bash
composer require drupal/opening_hours
```

Enable the module:

```bash
drush -y en opening_hours
```

Configure the opening hours module and set the Opening Hours API url via
`admin/admin/config/services/opening-hours`. 

## Usage

Adding opening hours information to an entity is done by adding an opening hours
field.

The opening hours information is loaded via ajax requests to the opening hours
backend.

## Synchronization

The opening hours information could be changed (service and channel label) or
could be removed from the opening hours backend. A drush command is available to 
synchronization the data and to flag opening hours fields with broken links.

- `drush opening-hours:sync all` : Synchronize all opening hours fields of all
  entities where they are used.
- `drush opening-hours:sync node_type` : Synchonize all opening hours fields of
  all entities of the provided entity type.
- `drush opening-hours:sync node_type:123` : Synchronize all opening hours
  fields of the provided entity type and entity id.
  
Broken links will be logged in the log.

There is an event that is triggered if a broken field is detected.
See `\Drupal\opening_hours\Event\FieldBrokenLinkEvent`
and `\Drupal\opening_hours\EventSubscriber\FieldSubscriber`.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed
recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md)
and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email security [at] gent.be
instead of using the issue tracker. See [SECURITY](SECURITY.md)

## Credits

- [Stad Gent][link-author-stadgent]
- [Digipolis Gent][link-author-digipolisgent]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File][link-license] for more
information.

## Roadmap and issue queue notes

This project is created and maintained by Digipolis Gent. During active
development, we use an internal issue tracker to guide/drive our development.
By working this way, we can link issues for other projects dependent on this
project, create cross-project boards, ... This allows our developers, project
leads and business analysts to have a better overview than we can create on
Github. This means that our roadmap won't be visible on here (for now).

We still look at the issue queue here and off course we welcome pull requests
with open arms! We are committed to creating and maintaining open source
projects. Questions about our approach can be asked through the issue queue
(except for security issues).

[ico-license]: https://img.shields.io/github/license/StadGent/drupal_module_opening-hours.svg?style=flat-square

[link-license]: LICENSE.md
[link-author-stadgent]: https://github.com/stadgent
[link-author-digipolisgent]: https://github.com/digipolisgent
[link-contributors]: ../../contributors
[Opening Hours platform]: https://github.com/StadGent/laravel_site_opening-hours
