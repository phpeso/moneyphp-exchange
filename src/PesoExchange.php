<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Peso\Money;

use Money\Currency;
use Override;
use Peso\Core\Requests\CurrentExchangeRateRequest;

final readonly class PesoExchange extends AbstractExchange
{
    #[Override]
    protected function createRequest(Currency $baseCurrency, Currency $counterCurrency): object
    {
        return new CurrentExchangeRateRequest($baseCurrency->getCode(), $counterCurrency->getCode());
    }
}
