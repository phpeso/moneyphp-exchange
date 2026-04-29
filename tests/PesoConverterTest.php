<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Peso\Money\Tests;

use Arokettu\Date\Calendar;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\CurrencyPair;
use Money\Money;
use Peso\Core\Services\ArrayService;
use Peso\Core\Services\NullService;
use Peso\Money\PesoConverter;
use PHPUnit\Framework\TestCase;

final class PesoConverterTest extends TestCase
{
    public function testCurrent(): void
    {
        $service = new ArrayService([
            'USD' => [
                'EUR' => '0.91234',
            ],
        ], [
            '2026-01-02' => [
                'USD' => [
                    'EUR' => '0.92345',
                ],
            ],
            '2026-02-03' => [
                'USD' => [
                    'EUR' => '0.93456',
                ],
            ],
        ]);

        $converter = new PesoConverter(new ISOCurrencies(), $service);
        $usd100 = Money::USD(10000);
        $eur = new Currency('EUR');
        $usd = new Currency('USD');
        $pair = new CurrencyPair($usd, $eur, '0.91234');

        self::assertEquals(Money::EUR(9123), $converter->convert($usd100, $eur));
        self::assertEquals([Money::EUR(9123), $pair], $converter->convertAndReturnWithCurrencyPair($usd100, $eur));
        self::assertEquals($pair, $converter->quote($usd, $eur));
    }

    public function testHistorical(): void
    {
        $service = new ArrayService([
            'USD' => [
                'EUR' => '0.91234',
            ],
        ], [
            '2026-01-02' => [
                'USD' => [
                    'EUR' => '0.92345',
                ],
            ],
            '2026-02-03' => [
                'USD' => [
                    'EUR' => '0.93456',
                ],
            ],
        ]);

        $converter = new PesoConverter(new ISOCurrencies(), $service);
        $usd100 = Money::USD(10000);
        $eur = new Currency('EUR');
        $usd = new Currency('USD');
        $pair1 = new CurrencyPair($usd, $eur, '0.92345');
        $pair2 = new CurrencyPair($usd, $eur, '0.93456');

        self::assertEquals(Money::EUR(9235), $converter->convertOnDate($usd100, $eur, '2026-01-02'));
        self::assertEquals(
            [Money::EUR(9346), $pair2],
            $converter->convertAndReturnWithCurrencyPairOnDate($usd100, $eur, new \DateTime('2026-02-03')),
        );
        self::assertEquals($pair1, $converter->quoteOnDate($usd, $eur, Calendar::parse('2026-01-02')));
    }

    public function testAgnostic(): void
    {
        $converter = new PesoConverter(new ISOCurrencies(), new NullService());

        $pair = new CurrencyPair(new Currency('USD'), new Currency('EUR'), '0.8888');
        $usd100 = Money::USD(10000);

        self::assertEquals(Money::EUR(8888), $converter->convertAgainstCurrencyPair($usd100, $pair));
    }
}
