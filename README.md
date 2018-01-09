# ImgLoader 

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]


This package is oriented for simple loading images from remote hosts via HTTP protocol. It uses [PSR-7](http://www.php-fig.org/psr/psr-7/).

## Requirements

Minimum PHP 7.1 is required.

CURL extension are needed.

This package uses some non-stable packages, so you must set your project's minimum stability to something like beta or dev in `composer.json`:

```
"minimum-stability": "dev",
"prefer-stable": true
```

If you don't the installation procedure below will fail.

## Install

This adapter satisfies the requirement for client-implementation and will make it possible to install the client with:

```bash
composer require mzapeka/img_php_loader
```

## Usage - simple

Simplest possible use case:

```php
$imgLoader = new ImgLoader();
try {
        //setup the path to folder with images
    $imgLoader->setPicFolder('test_folder');
        //setup the URL of remout host with images
    $imgLoader->setUrl('https://test.com/catalog/index.php');
    $imgLoader->uploadImages();
} catch (Exception $e){
    echo $e->getMessage();
}
```

That's it, this is all you need to get started.


## Testing

Just run PHPUnit in the root folder of the cloned project.
Some calls do require an internet connection (see `tests/Factory/EntityTest`).

```bash
phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email mzapeka@gmail.com instead of using the issue tracker.

## Credits

- [Mykola Zapeka][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/Mzapeka/ImgLoader.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/Mzapeka/ImgLoader/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/Mzapeka/ImgLoader.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Mzapeka/ImgLoader.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/Mzapeka/ImgLoader.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/Mzapeka/ImgLoader
[link-travis]: https://travis-ci.org/Mzapeka/ImgLoader
[link-scrutinizer]: https://scrutinizer-ci.com/g/Mzapeka/ImgLoader/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/Mzapeka/ImgLoader
[link-downloads]: https://packagist.org/packages/Mzapeka/ImgLoader
[link-author]: https://github.com/Mzapeka
[link-contributors]: ../../contributors
