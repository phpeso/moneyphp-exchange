<?php

declare(strict_types=1);

use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange;
use Peso\Core\Exceptions\PesoException;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Services\CurrentExchangeRateServiceInterface;

final readonly class PesoExchange implements Exchange
{
    public function __construct(
        private CurrentExchangeRateServiceInterface $service,
    ) {
    }

    public function quote(Currency $baseCurrency, Currency $counterCurrency): CurrencyPair
    {
        try {
            $rate = $this->service->send(new CurrentExchangeRateRequest(
                $baseCurrency->getCode(),
                $counterCurrency->getCode(),
            ));
        } catch (PesoException) {
            throw UnresolvableCurrencyPairException::createFromCurrencies($baseCurrency, $counterCurrency);
        }

        return new CurrencyPair($baseCurrency, $counterCurrency, $rate->value);
    }
}
