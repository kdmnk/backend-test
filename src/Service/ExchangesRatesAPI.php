<?php
namespace Opeepl\BackendTest\Service;

/**
 * Main entrypoint for this library.
 */
class ExchangesRatesAPI extends ServiceAPI {

    private $api_key;
    private $url;

    public function __construct()
    {
        parent::__construct();
        $this->api_key = $this->config['api_key'];
        $this->url = $this->config['url'];
    }

    /**
     * Return all supported currencies
     *
     * @return array<string>
     * @throws InvalidRespone
     */
    public function getSupportedCurrencies(): array
    {
        $response = $this->makeRequest('symbols');

        if(!isset($response) || !isset($response['symbols'])) {
            throw new InvalidRespone("Result can not be obtained.");
        }

        return array_keys($response['symbols']);
    }

    /**
     * Given the $amount in $fromCurrency, it returns the corresponding amount in $toCurrency.
     *
     * @param int $amount
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return int
     * @throws InvalidRespone
     */
    public function getExchangeAmount(int $amount, string $fromCurrency, string $toCurrency): int
    {
        $parameters = [
            'amount' => $amount,
            'from' => $fromCurrency,
            'to' => $toCurrency,
        ];
        $response = $this->makeRequest('convert', 'GET', $parameters);

        var_dump($response);

        if(!isset($response) || !isset($response['result'])) {
            throw new InvalidRespone("Result can not be obtained.");
        }
        return $response['result'];
    }

    /**
     * Make an API Request
     *
     * @param string $method HTTP method ("GET"/"POST")
     * @param $endpoint string endpoint name
     * @param array $parameters request parameters
     * @return array
     */
    private function makeRequest(string $endpoint, string $method = 'GET', array $parameters = []) : array
    {
        $parameters = $this->convertParameters($parameters);
        $url = $this->url."/".$endpoint.$parameters;

        /**
         * @author https://apilayer.com/marketplace/exchangerates_data-api#endpoints
         */
        {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: text/plain",
                    "apikey: ".$this->api_key
                ),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $method
            ));

            $response = curl_exec($curl);

            curl_close($curl);
        }

        return $this->parseResponse($response);

    }

    /**
     * Convert an array to a valid GET parameter string.
     * Note: POST (or other) parameters are currently not supported.
     * @param array $parameters
     * @return string
     */
    private function convertParameters(array $parameters = []) : string
    {
        $string = implode("&",
            array_map(function($key, $value) { return $key."=".$value; },
                array_keys($parameters), $parameters
            )
        );
        return $string ? "?" . $string : "";
    }

    /**
     * Parse the API response to a PHP array
     * @param string $response
     * @return array
     */
    private function parseResponse(string $response): array
    {
        if(!$response) return [];

        return json_decode($response, true);
    }
}
