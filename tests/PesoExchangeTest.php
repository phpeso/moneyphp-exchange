<?php

declare(strict_types=1);

namespace Peso\Money\Tests;

use Money\Converter;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Money;
use Peso\Core\Services\ArrayService;
use Peso\Money\PesoExchange;
use PHPUnit\Framework\TestCase;

class PesoExchangeTest extends TestCase
{
    public function testExchange(): void
    {
        $service = new ArrayService([
            'USD' => [
                'EUR' => '0.91234',
            ],
        ]);
        $exchange = new PesoExchange($service);
        $converter = new Converter(new ISOCurrencies(), $exchange);

        $usd100 = Money::USD(10000);

        $eur = $converter->convert($usd100, new Currency('EUR'));

        self::assertEquals('9123', $eur->getAmount());
    }

    public function testUnresolvable(): void
    {
        $service = new ArrayService([
            'USD' => [
                'EUR' => '0.91234',
            ],
        ]);
        $exchange = new PesoExchange($service);
        $converter = new Converter(new ISOCurrencies(), $exchange);

        $eur100 = Money::EUR(10000);

        self::expectException(UnresolvableCurrencyPairException::class);
        self::expectExceptionMessage('Cannot resolve a currency pair for currencies: EUR/USD');

        $converter->convert($eur100, new Currency('USD'));
    }
}
