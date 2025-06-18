<?php

declare(strict_types=1);

namespace Peso\Money;

use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange;
use Override;
use Peso\Core\Responses\ErrorResponse;
use Peso\Core\Services\ExchangeRateServiceInterface;

abstract readonly class AbstractExchange implements Exchange
{
    public function __construct(
        protected ExchangeRateServiceInterface $service,
    ) {
    }

    abstract protected function createRequest(Currency $baseCurrency, Currency $counterCurrency): object;

    #[Override]
    public function quote(Currency $baseCurrency, Currency $counterCurrency): CurrencyPair
    {
        $request = $this->createRequest($baseCurrency, $counterCurrency);
        $response = $this->service->send($request);

        if ($response instanceof ErrorResponse) {
            throw UnresolvableCurrencyPairException::createFromCurrencies($baseCurrency, $counterCurrency);
        }

        return new CurrencyPair($baseCurrency, $counterCurrency, $response->rate->value);
    }
}
