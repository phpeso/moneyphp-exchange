<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Peso\Money;

use Arokettu\Date\Calendar;
use Arokettu\Date\Date;
use DateTimeInterface;
use Money\Converter;
use Money\Currencies;
use Money\Currency;
use Money\CurrencyPair;
use Money\Money;
use Peso\Core\Services\PesoServiceInterface;

final readonly class PesoConverter
{
    private Converter $currentConverter;
    private PesoExchange $currentExchange;

    public function __construct(
        private Currencies $currencies,
        private PesoServiceInterface $service,
    ) {
        $this->currentExchange = new PesoExchange($this->service);
        $this->currentConverter = new Converter($currencies, new PesoExchange($this->service));
    }

    private function normalizeDate(string|DateTimeInterface|Date $date): Date
    {
        if (\is_string($date)) {
            return Calendar::parse($date);
        }
        if ($date instanceof DateTimeInterface) {
            return Calendar::fromDateTime($date);
        }
        return $date;
    }

    private function historicalExchange(string|DateTimeInterface|Date $date): PesoHistoricalExchange
    {
        return new PesoHistoricalExchange($this->service, $this->normalizeDate($date));
    }

    private function historicalConverter(string|DateTimeInterface|Date $date): Converter
    {
        return new Converter($this->currencies, $this->historicalExchange($date));
    }

    /**
     * @param Money::ROUND_* $roundingMode
     */
    public function convert(
        Money $money,
        Currency $counterCurrency,
        int $roundingMode = Money::ROUND_HALF_UP,
    ): Money {
        return $this->currentConverter->convert($money, $counterCurrency, $roundingMode);
    }

    /**
     * @param Money::ROUND_* $roundingMode
     *
     * @return array{0: Money, 1: CurrencyPair}
     */
    public function convertAndReturnWithCurrencyPair(
        Money $money,
        Currency $counterCurrency,
        int $roundingMode = Money::ROUND_HALF_UP,
    ): array {
        return $this->currentConverter->convertAndReturnWithCurrencyPair($money, $counterCurrency, $roundingMode);
    }

    public function quote(Currency $baseCurrency, Currency $counterCurrency): CurrencyPair
    {
        return $this->currentExchange->quote($baseCurrency, $counterCurrency);
    }

    /**
     * @param Money::ROUND_* $roundingMode
     */
    public function convertOnDate(
        Money $money,
        Currency $counterCurrency,
        string|DateTimeInterface|Date $date,
        int $roundingMode = Money::ROUND_HALF_UP,
    ): Money {
        return $this->historicalConverter($date)->convert($money, $counterCurrency, $roundingMode);
    }

    /**
     * @param Money::ROUND_* $roundingMode
     *
     * @return array{0: Money, 1: CurrencyPair}
     */
    public function convertAndReturnWithCurrencyPairOnDate(
        Money $money,
        Currency $counterCurrency,
        string|DateTimeInterface|Date $date,
        int $roundingMode = Money::ROUND_HALF_UP,
    ): array {
        return $this->historicalConverter($date)
            ->convertAndReturnWithCurrencyPair($money, $counterCurrency, $roundingMode);
    }

    public function quoteOnDate(
        Currency $baseCurrency,
        Currency $counterCurrency,
        string|DateTimeInterface|Date $date,
    ): CurrencyPair {
        return $this->historicalExchange($date)->quote($baseCurrency, $counterCurrency);
    }

    /**
     * @param Money::ROUND_* $roundingMode
     */
    public function convertAgainstCurrencyPair(
        Money $money,
        CurrencyPair $currencyPair,
        int $roundingMode = Money::ROUND_HALF_UP,
    ): Money {
        return $this->currentConverter->convertAgainstCurrencyPair($money, $currencyPair, $roundingMode);
    }
}
