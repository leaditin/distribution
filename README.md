# Leaditin\Distribution

A simple PHP API for distributing values based on their probabilities

[![Build Status][ico-build]][link-build]
[![Code Quality][ico-code-quality]][link-code-quality]
[![Code Coverage][ico-code-coverage]][link-code-coverage]
[![Latest Version][ico-version]][link-packagist]
[![PDS Skeleton][ico-pds]][link-pds]

## Installation

The preferred method of installation is via [Composer](http://getcomposer.org/). Run the following command to install the latest version of a package and add it to your project's `composer.json`:

```bash
composer require leaditin/distribution
```

## Usage

Imagine that you want to simulate creation of 100 users where each must have defined gender.
You want to have 53% female and 47% male.

Imagine that you do not want to generate all female users and after that all male users, instead of that you want these records to be generated randomly.

This is where `Leaditin\Distribution` will help you:

```php
use Leaditin\Distribution\Collection;
use Leaditin\Distribution\Distributor;
use Leaditin\Distribution\Element;
use Leaditin\Distribution\Exception\DistributorException;

$probabilities = new Collection(
    new Element('MALE', 53),
    new Element('FEMALE', 47)
);

$distributor = new Distributor($probabilities, 100);

# Create user with random gender
$user = new \User();
$user->gender = $distributor->useRandomCode();
$user->save();

# Create user with explicit gender
$user = new \User();
$user->firstName = 'Jon';
$user->lastName = 'Snow';
$user->gender = $distributor->useCode('MALE');
$user->save();
```

## Credits

- [All Contributors][link-contributors]

## License

Released under MIT License - see the [License File](LICENSE) for details.


[ico-version]: https://img.shields.io/packagist/v/leaditin/distribution.svg
[ico-build]: https://travis-ci.org/leaditin/distribution.svg?branch=master
[ico-code-coverage]: https://img.shields.io/scrutinizer/coverage/g/leaditin/distribution.svg
[ico-code-quality]: https://img.shields.io/scrutinizer/g/leaditin/distribution.svg
[ico-pds]: https://img.shields.io/badge/pds-skeleton-blue.svg

[link-packagist]: https://packagist.org/packages/leaditin/distribution
[link-build]: https://travis-ci.org/leaditin/distribution
[link-code-coverage]: https://scrutinizer-ci.com/g/leaditin/distribution/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/leaditin/distribution
[link-pds]: https://github.com/php-pds/skeleton
[link-contributors]: ../../contributors
