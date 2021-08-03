# Very short description of the package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rawaby88/portal.svg?style=flat-square)](https://packagist.org/packages/rawaby88/portal)
[![Total Downloads](https://img.shields.io/packagist/dt/rawaby88/portal.svg?style=flat-square)](https://packagist.org/packages/rawaby88/portal)
![GitHub Actions](https://github.com/rawaby88/portal/actions/workflows/main.yml/badge.svg)

This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what PSRs you support to avoid any confusion with users and contributors.

## Installation

You can install the package via composer:

```bash
composer require rawaby88/portal
```

## Usage

```php
// Usage description here
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email github@dreamod.pl instead of using the issue tracker.

## Credits

-   [Mahmoud Osman](https://github.com/rawaby88)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).


##Migration Customization
If you are not going to use Sanctum's default migrations, you should call the Sanctum::ignoreMigrations method in the register method of your App\Providers\AppServiceProvider class. You may export the default migrations by executing the following command: php artisan vendor:publish --tag=sanctum-migrations