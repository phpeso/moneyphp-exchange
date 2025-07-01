<?php

declare(strict_types=1);

namespace Peso\Money\Tests;

use Arokettu\Date\Calendar;
use Money\Converter;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Money;
use Peso\Core\Services\ArrayService;
use Peso\Money\PesoHistoricalExchange;
use PHPUnit\Framework\TestCase;

final class PesoHistoricalExchangeTest extends TestCase
{
    public function testExchange(): void
    {
        $service = new ArrayService(historicalRates: [
            '2025-06-13' => [
                'USD' => [
                    'EUR' => '0.91234',
                ],
            ],
            '2025-06-19' => [
                'USD' => [
                    'EUR' => '0.94321',
                ],
            ],
        ]);
        $exchange = new PesoHistoricalExchange($service, Calendar::parse('2025-06-13'));
        $converter = new Converter(new ISOCurrencies(), $exchange);

        $usd100 = Money::USD(10000);

        $eur = $converter->convert($usd100, new Currency('EUR'));
        self::assertEquals('9123', $eur->getAmount());

        $exchange = $exchange->withDate(Calendar::parse('2025-06-19'));
        $converter = new Converter(new ISOCurrencies(), $exchange);

        $eur = $converter->convert($usd100, new Currency('EUR'));
        self::assertEquals('9432', $eur->getAmount());
    }

    public function testUnresolvable(): void
    {
        $service = new ArrayService(historicalRates: [
            '2025-06-13' => [
                'USD' => [
                    'EUR' => '0.91234',
                ],
            ],
            '2025-06-19' => [
                'USD' => [
                    'EUR' => '0.94321',
                ],
            ],
        ]);
        $exchange = new PesoHistoricalExchange($service, Calendar::parse('2025-06-14'));
        $converter = new Converter(new ISOCurrencies(), $exchange);

        $usd100 = Money::USD(10000);

        self::expectException(UnresolvableCurrencyPairException::class);
        self::expectExceptionMessage('Cannot resolve a currency pair for currencies: USD/EUR');

        $converter->convert($usd100, new Currency('EUR'));
    }
}
