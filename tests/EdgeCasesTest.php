<?php

declare(strict_types=1);

namespace Peso\Money\Tests;

use Arokettu\Date\Date;
use Error;
use Money\Converter;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;
use Peso\Core\Responses\ConversionResponse;
use Peso\Core\Services\PesoServiceInterface;
use Peso\Core\Types\Decimal;
use Peso\Money\PesoExchange;
use PHPUnit\Framework\TestCase;

final class EdgeCasesTest extends TestCase
{
    public function testBrokenService(): void
    {
        $service = new class implements PesoServiceInterface
        {
            public function send(object $request): ConversionResponse
            {
                // the service must return ExchangeRateResponse for *ExchangeRateRequest
                return new ConversionResponse(new Decimal('1'), Date::today());
            }

            public function supports(object $request): bool
            {
                return true;
            }
        };

        $exchange = new PesoExchange($service);
        $converter = new Converter(new ISOCurrencies(), $exchange);

        $this->expectException(Error::class);
        $this->expectExceptionMessage(
            'Broken Service object: the response must be an instance of ExchangeRateResponse|ErrorResponse',
        );

        $converter->convert(Money::EUR(100), new Currency('USD'));
    }
}
