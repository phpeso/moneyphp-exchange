<?php

declare(strict_types=1);

use Arokettu\Date\Date;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange;
use Peso\Core\Exceptions\PesoException;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use Peso\Core\Services\HistoricalExchangeRateServiceInterface;

final readonly class PesoHistoricalExchange implements Exchange
{
    public function __construct(
        private HistoricalExchangeRateServiceInterface $service,
        private Date $date,
    ) {
    }

    public function withDate(Date $date): void
    {
        new self($this->service, $date);
    }

    public function quote(Currency $baseCurrency, Currency $counterCurrency): CurrencyPair
    {
        try {
            $rate = $this->service->send(new HistoricalExchangeRateRequest(
                $baseCurrency->getCode(),
                $counterCurrency->getCode(),
                $this->date,
            ));
        } catch (PesoException) {
            throw UnresolvableCurrencyPairException::createFromCurrencies($baseCurrency, $counterCurrency);
        }

        return new CurrencyPair($baseCurrency, $counterCurrency, $rate->value);
    }
}
