# A package that uses redis geo functions for providing super fast lookups. 

[![Latest Version on Packagist](https://img.shields.io/packagist/v/leitom/geo.svg?style=flat-square)](https://packagist.org/packages/leitom/geo)
[![Build Status](https://img.shields.io/travis/leitom/geo/master.svg?style=flat-square)](https://travis-ci.org/leitom/geo)
[![Quality Score](https://img.shields.io/scrutinizer/g/leitom/geo.svg?style=flat-square)](https://scrutinizer-ci.com/g/leitom/geo)
[![Total Downloads](https://img.shields.io/packagist/dt/leitom/geo.svg?style=flat-square)](https://packagist.org/packages/leitom/geo)

This package takes a different approach for building geo location apps.
Instead of using something like mysql geospatial or querying with other sql functions this package uses redis built in geospatial functionality.

With this approach there are some realy nice benefits, you can develop your applications with sqlite only instead of having a mix og mysql and sqlite or making your whole test suite based on mysql.
Redis is super fast and your lookups will be way faster than any thing else.

## Installation

You can install the package via composer:

```bash
composer require leitom/geo
```

## Usage

``` php
// Usage description here
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email leirvik.tommy@gmail.com instead of using the issue tracker.

## Credits

- [Tommy Leirvik](https://github.com/leitom)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
