<?php

declare(strict_types=1);

namespace Peso\MoneyPHP;

use Arokettu\Date\Date;
use Money\Currency;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use Peso\Core\Services\ExchangeRateServiceInterface;

final readonly class PesoHistoricalExchange extends AbstractExchange
{
    public function __construct(
        ExchangeRateServiceInterface $service,
        private Date $date,
    ) {
        parent::__construct($service);
    }

    public function withDate(Date $date): void
    {
        new self($this->service, $date);
    }

    protected function createRequest(Currency $baseCurrency, Currency $counterCurrency): object
    {
        return new HistoricalExchangeRateRequest($baseCurrency->getCode(), $counterCurrency->getCode(), $this->date);
    }
}
