# Ibexa Kernel

This package is part of [Ibexa DXP](https://ibexa.co).

To use this package, [install Ibexa DXP](https://doc.ibexa.co/en/latest/install/).

This package contains an advanced Content Model, allowing to structure any kind of content or content-like data in a future-proof Content Repository. 
Ibexa Kernel also aims to provide additional features for the MVC layer (Symfony) to increase your productivity [Ibexa DXP](https://ibexa.co).

## Current Organization

MVC layer:
- [eZ/Bundle](eZ/Bundle) - the bundles that are important to expose the functionality of the Backend and MVC layer to Symfony.
- [eZ/Publish/Core/MVC](eZ/Publish/Core/MVC) - the parts that make up the different components extending Symfony.
- [eZ/Publish/Core/Pagination](eZ/Publish/Core/Pagination) - a component extending PagerFanta for pagination of eZ Platform search queries.

Backend:
- [eZ/Publish/API](eZ/Publish/API) - the definition of stable interfaces for the PHP *Public* API, mainly Content *Repository API*.
- [eZ/Publish/SPI/Persistence](eZ/Publish/SPI/Persistence) - a layer which is not frozen yet, meaning it might change in between releases. Those are persistence interfaces for Storage Engine.
- [eZ/Publish/SPI](eZ/Publish/SPI) - (anything other than Persistence) is frozen and has a Backward Compatibility promise of Service Provider Interface, meaning no breaking changes both from consumption and implementation POV.
- [eZ/Publish/Core](eZ/Publish/Core) - implementations of both APIs and SPIs; the naming aims to map to name of the interface they implement. For example, `Core\Persistence\Legacy` being implementation of `SPI\Persistence`.

## Testing Locally

This kernel contains a comprehensive set of unit, functional, and integration tests. At the time of writing, 9k unit tests, 8k integration tests, and several functional tests.

**Dependencies**
* **PHP 7 Modules**: php7\_intl php7\_xsl php7\_gd php7\_sqlite *(aka `pdo\_sqlite`)*
* **Database**: sqlite3, optionally: mysql/postgres *if so make sure to have relevant pdo modules installed*

For Contributing to this Bundle, you should make sure to run both unit and integration tests.

1. Set up this repository locally:

    ```bash
    # Note: Change the line below to the ssh format of your fork to create topic branches to propose as pull requests
    git clone https://github.com/ezsystems/ezplatform-kernel.git
    cd ezplatform-kernel
    composer install
    ```
2. Run unit tests:

    At this point you should be able to run unit tests:
    ```bash
    php -d memory_limit=-1 vendor/bin/phpunit
    ```

3. Run integration tests:

    ```bash
    # If you want to test against mysql or postgres instead of sqlite, define one of these with reference to an empty test db:
    # export DATABASE="mysql://root@localhost/$DB_NAME"
    # export DATABASE="pgsql://postgres@localhost/$DB_NAME"
    php -d memory_limit=-1 vendor/bin/phpunit -c phpunit-integration-legacy.xml
    ```

    To run integration tests against Solr, see [Solr Search Engine Bundle for Ibexa DXP](https://github.com/ezsystems/ezplatform-solr-search-engine).

## COPYRIGHT

Copyright (C) 1999-2021 Ibexa AS (formerly eZ Systems AS). All rights reserved.

## LICENSE

This source code is available separately under the following licenses:

A - Ibexa Business Use License Agreement (Ibexa BUL),
version 2.4 or later versions (as license terms may be updated from time to time)
Ibexa BUL is granted by having a valid Ibexa DXP (formerly eZ Platform Enterprise) subscription,
as described at: https://www.ibexa.co/product
For the full Ibexa BUL license text, please see:
https://www.ibexa.co/software-information/licenses-and-agreements (latest version applies)

AND

B - GNU General Public License, version 2
Grants an copyleft open source license with ABSOLUTELY NO WARRANTY. For the full GPL license text, please see:
https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
