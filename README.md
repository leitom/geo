# A package that uses Redis geospatial functions for providing super fast searches. 

[![Latest Version on Packagist](https://img.shields.io/packagist/v/leitom/geo.svg?style=flat-square)](https://packagist.org/packages/leitom/geo)
[![Build Status](https://img.shields.io/travis/leitom/geo/master.svg?style=flat-square)](https://travis-ci.org/leitom/geo)
[![Quality Score](https://img.shields.io/scrutinizer/g/leitom/geo.svg?style=flat-square)](https://scrutinizer-ci.com/g/leitom/geo)
[![Total Downloads](https://img.shields.io/packagist/dt/leitom/geo.svg?style=flat-square)](https://packagist.org/packages/leitom/geo)

This package makes it easy to add geospatial to your Laravel applications.
It uses Redis built in geospatial features and combine this with your Laravel Models in an elegant way.

## Installation

You can install the package via composer:

```bash
composer require leitom/geo
```

## Usage

The package comes with a simple trait thats integrates with Laravel Eloquent models but also a facade to interact with the different geospatial functions.

### Laravel Eloquent integration

All you have to do when integrating this package with one or more of your Eloquent models is to use the ```Leitom\Geo\HasGeoAbilities``` trait.
This trait will take care of keeping the Redis index in sync with your models due to the use of a model observer so you dont have to think about it.
Each time you create, save or delete a model the Redis index will be updated to reflect the changes.

``` php
use Leitom\Geo\HasGeoAbilities;

class User extends Model
{
    use HasGeoAbilities;

    protected $fillable = ['id', 'name', 'longitude', 'latitude'];
}
```

### Search

Here we perform a search with Longitude, Latitude and Radius.
The unit of the radius is configured in the configuration file, default km = kilometers. With the 4. argument to the ```geoSearch``` function you can specify the sorting, default ```ASC```.

``` php
$users = User::geoSearch(-115.17258, 36.11996, 10)->get();
```

The package adds two attributes to every model who implements the HasGeoAbilities. We add the unit and distance wich you can display to the end user.

```php
$user->geoUnit // km
$user->geoDistance; // 0.999
```

The ```toArray()``` function is also overrided so that these two attributes would be loaded.

``` php
[
    'geo_unit' => 'km',
    'geo_distance' => 0.999,
]
```

### Find distance between two models

To find the distance between two models you can use the ```geoDistanceFrom``` function on a given model

``` php
$userA = User::findOrFail(1);
$userB = User::findOrFail(2);

$userA->geoDistanceFrom($userB) // ['unit' => 'km', 'distance' => 0.999]
```

### Get the nearest models

``` php
$userA = User::findOrFail(1);
$userB = User::findOrFail(2);

$users = $userA->geoNearest()->paginate(5);
```

### Perform a search via the ```Geo``` facade

``` php
$locations = Leitom\Geo\Facades\Geo::index('cars')->search(-115.17258, 36.11996, 10);
```

### Get the distance between two locations

``` php
$locationA = new Leitom\Geo\Coordinate('my-car', -115.17087, 36.12306);
$locationB = new Leitom\Geo\Coordinate('robins-car', -115.171971, 36.120609);

Leitom\Geo\Facades\Geo::index('cars')->between($locationA, $locationB); // 0.2900
```

### Get a list of the nearest locations from a given location

``` php
$locationA = new Leitom\Geo\Coordinate('my-car', -115.17087, 36.12306);

Leitom\Geo\Facades\Geo::index('cars')->from($locationA, 10, 'ASC'); // [['my-car' => 0], ['your-car' => 0.2900]]
```

### Add a coordinate to an index

``` php
$locationA = new Leitom\Geo\Coordinate('my-car', -115.17087, 36.12306);

Leitom\Geo\Facades\Geo::index('cars')->add($locationA);
```

### Add multiple coordinates to an index

``` php
$locationA = new Leitom\Geo\Coordinate('my-car', -115.17087, 36.12306);
$locationB = new Leitom\Geo\Coordinate('robins-car', -115.171971, 36.120609);

Leitom\Geo\Facades\Geo::index('cars')->add($locationA, $locationB);

Leitom\Geo\Facades\Geo::index('cars')->add([$locationA, $locationB]);
```

### Remove coordinates from an index

``` php
Leitom\Geo\Facades\Geo::index('cars')->remove('my-car');

Leitom\Geo\Facades\Geo::index('cars')->remove('my-car', 'robins-car');
```

### Import and Remove existing models

The package include two commands to handle import and remove models from the Redis index. This is usefull if you integrate this package in an existing project.

``` bash
php artisan geo:import App\User
```

``` bash
php artisan geo:remove App\User
```

### Testing

Ensure you have Redis installed.
If you are not using the default configuration for Redis you can change environment variables in the phpunit.xml file.

``` bash
composer test
```

When testing you own application logic you should include the ```Leitom\Geo\Tests\RefreshGeo``` trait. This will ensure that you start with a fresh Redis index inside your tests. Just like the built in ```RefreshDatabase``` trait in Laravel.

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
