<?php
namespace Opeepl\BackendTest\Service;

use CompileError;

/**
 * Abstract class for exchange rate APIs.
 * Imports the config for the corresponding api from the src/config.php as a property.
 */
abstract class ServiceAPI  {

    protected $config;

    public function __construct()
    {
        $config = require 'src/config.php';
        $id = basename(get_class($this));
        if($config && isset($config[$id])) {
            $this->config = $config[$id];
        } else throw new CompileError(
            "The config file does not exists or it does not contain an entry named '".$id."'."
        );
    }

    /**
     * Return all supported currencies
     *
     * @return array<string>
     */
    public abstract function getSupportedCurrencies(): array;

    /**
     * Given the $amount in $fromCurrency, it returns the corresponding amount in $toCurrency.
     *
     * @param int $amount
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return int
     */
    public abstract function getExchangeAmount(int $amount, string $fromCurrency, string $toCurrency): int;
}
