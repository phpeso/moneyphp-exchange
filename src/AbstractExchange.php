<?php

declare(strict_types=1);

namespace Peso\Money;

use Error;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange;
use Override;
use Peso\Core\Responses\ErrorResponse;
use Peso\Core\Responses\ExchangeRateResponse;
use Peso\Core\Services\PesoServiceInterface;

abstract readonly class AbstractExchange implements Exchange
{
    public function __construct(
        protected PesoServiceInterface $service,
    ) {
    }

    abstract protected function createRequest(Currency $baseCurrency, Currency $counterCurrency): object;

    #[Override]
    public function quote(Currency $baseCurrency, Currency $counterCurrency): CurrencyPair
    {
        // obvious optimization
        if ($baseCurrency->getCode() === $counterCurrency->getCode()) {
            return new CurrencyPair($baseCurrency, $counterCurrency, '1');
        }

        $request = $this->createRequest($baseCurrency, $counterCurrency);
        $response = $this->service->send($request);

        if ($response instanceof ExchangeRateResponse) {
            return new CurrencyPair($baseCurrency, $counterCurrency, $response->rate->value);
        }
        if ($response instanceof ErrorResponse) {
            throw UnresolvableCurrencyPairException::createFromCurrencies($baseCurrency, $counterCurrency);
        }

        throw new Error(
            'Broken Service object: the response must be an instance of ExchangeRateResponse|ErrorResponse',
        );
    }
}
