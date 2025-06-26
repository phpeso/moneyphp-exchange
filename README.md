# Peso-based Exchange class for Money

[![Packagist]][Packagist Link]
[![PHP]][Packagist Link]
[![License]][License Link]

[Packagist]: https://img.shields.io/packagist/v/peso/moneyphp-exchange.svg?style=flat-square
[PHP]: https://img.shields.io/packagist/php-v/peso/moneyphp-exchange.svg?style=flat-square
[License]: https://img.shields.io/packagist/l/peso/moneyphp-exchange.svg?style=flat-square

[Packagist Link]: https://packagist.org/packages/peso/moneyphp-exchange
[License Link]: LICENSE.md

This is a library that provides an Exchange class for the Money for PHP based on the Peso for PHP.

## Installation

```bash
composer require peso/moneyphp-exchange
```

## Example

```php
<?php

use Money\Converter;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;
use Peso\Money\PesoExchange;
use Peso\Services\EuropeanCentralBankService;

require __DIR__ . '/vendor/autoload.php';

$exchange = new PesoExchange(new EuropeanCentralBankService());
$converter = new Converter(new ISOCurrencies(), $exchange);

$eur100 = Money::EUR(10000);

var_dump($converter->convert($eur100, new Currency('USD'))); // Money::USD(...)
```

## Documentation

Read the full documentation here: <https://phpeso.org/v1.x/integrations/moneyphp-exchange.html>

## Support

Please file issues on our main repo at GitHub: <https://github.com/phpeso/moneyphp-exchange/issues>

## License

The library is available as open source under the terms of the [MIT License][License Link].
