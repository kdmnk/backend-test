<?php
namespace Opeepl\BackendTest\Service;

/**
 * Main entrypoint for this library.
 */
class ExchangeRateService {

    public ServiceAPI $exchangesRatesAPI;

    public function __construct()
    {
        $this->exchangesRatesAPI = new ExchangesRatesAPI();
    }

    /**
     * Return all supported currencies
     *
     * @return array<string>
     */
    public function getSupportedCurrencies(): array {
        return $this->exchangesRatesAPI->getSupportedCurrencies();
    }

    /**
     * Given the $amount in $fromCurrency, it returns the corresponding amount in $toCurrency.
     *
     * @param int $amount
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return int
     */
    public function getExchangeAmount(int $amount, string $fromCurrency, string $toCurrency): int {

        return $this->exchangesRatesAPI->getExchangeAmount($amount, $fromCurrency, $toCurrency);
    }
}
