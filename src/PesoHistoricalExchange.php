<?php

declare(strict_types=1);

namespace Peso\Money;

use Arokettu\Date\Date;
use Money\Currency;
use Override;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use Peso\Core\Services\PesoServiceInterface;

final readonly class PesoHistoricalExchange extends AbstractExchange
{
    public function __construct(
        PesoServiceInterface $service,
        private Date $date,
    ) {
        parent::__construct($service);
    }

    public function withDate(Date $date): self
    {
        return new self($this->service, $date);
    }

    #[Override]
    protected function createRequest(Currency $baseCurrency, Currency $counterCurrency): object
    {
        return new HistoricalExchangeRateRequest($baseCurrency->getCode(), $counterCurrency->getCode(), $this->date);
    }
}
