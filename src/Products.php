<?php

namespace astroselling\Jupiter;

use stdClass;

class Products
{
    protected string $version = "Jupiter SDK v1.11";
    protected string $url;
    protected string $token;


    /**
     * Create a new Jupiter API Client with provided API keys
     *
     * @param string $url
     * @param string $apiToken
     */
    public function __construct(string $url = '', string $apiToken = '')
    {
        $this->url = $url;
        $this->token = $apiToken;
    }


    /**
     * Display SDK version
     *
     * @return string
     */
    public function version(): string
    {
        return $this->version;
    }


    public function getUrl(): string
    {
        return $this->url;
    }

    public function getApiToken(): string
    {
        return $this->token;
    }

    public function sendRequest(string $url, array $header = null, object $content = null, string $type = 'POST', bool $xml = false): object
    {
        ini_set('max_execution_time', 3000);
        ini_set('memory_limit', '1024M');

        $result = new \stdClass();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        // http 2 support ...
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        if ($content) {
            if (!$xml) {
                $fields = json_encode($content, JSON_UNESCAPED_UNICODE);
            } else {
                curl_setopt($curl, CURLOPT_POST, count($content));
                $fields = http_build_query($content);
            }

            curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
        }

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $type);

        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        if ($xml) {
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        }

        if ($header) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }

        $response = curl_exec($curl);
        $error    = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($httpCode < 200 || $httpCode > 302) {
            throw new \Exception('Jupiter CURL Exception: ' . $response, $httpCode);
        }

        if ($error || !$response) {
            $result = new \stdClass();
            $result->error = $error;
        } else {
            $curlResponse = json_decode($response);
            //print_r($curlResponse);
            if (!is_object($curlResponse)) {
                $result->data = (object) $curlResponse;
            } else {
                $result = $curlResponse;
            }
        }

        // keep http code
        $result->httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        return $result;
    }

    public function getHeader(): array
    {

        return array(
                    "Cache-Control: no-cache",
                    "Content-Type: application/json",
                     "Accept: application/json"
                    );
    }


    public function getChannels(): object
    {
        $action = "channels?api_token=" . $this->getApiToken();
        $url = $this->getUrl() . $action;
        $header = $this->getHeader();
        return $this->sendRequest($url, $header, null, 'GET');
    }


    public function hasChannel(string $channel): bool
    {
        $exist = false;

        $channels = $this->getChannels();

        if (isset($channels->data)) {
            foreach ($channels->data as $ch) {
                if ($ch->id == $channel) {
                    $exist = true;
                    break;
                }
            }
        }

        return $exist;
    }

    public function createProduct(string $channel, object $product): bool
    {
        $updated = false;

        $action = "channels/{$channel}/products?api_token=" . $this->getApiToken();
        $url = $this->getUrl() . $action;
        $header = $this->getHeader();
        $response = $this->sendRequest($url, $header, $product, 'POST');

        $httpCode = $response->httpcode ?? 500;

        if ($httpCode == 200) {
            $updated = true;
        }

        return $updated;
    }


    public function updateProduct(string $channel, object $product): bool
    {
        $updated = false;

        $id_in_channel = $product->id_in_erp;
        $action = "channels/{$channel}/products/{$id_in_channel}?api_token=" . $this->getApiToken();
        $url = $this->getUrl() . $action;
        $header = $this->getHeader();
        $response = $this->sendRequest($url, $header, $product, 'PUT');

        $httpCode = $response->httpcode ?? 500;

        if ($httpCode == 200) {
            $updated = true;
        }

        // si no existe el producto, lo mando crear ..
        if ($httpCode == 404) {
            $updated = $this->createProduct($channel, $product);
        }

        return $updated;
    }

    public function getProducts(string $channel, int $limit = 500): array
    {
        $products = array();
        $empty    = array();
        $error    = false;

        $next   = true;
        $page   = 1;
        $offset = 0;

        while ($next) {
            $action = "channels/{$channel}/products?api_token=" . $this->getApiToken() . "&limit={$limit}&offset={$offset}";
            $url = $this->getUrl() . $action;
            $header = $this->getHeader();

            $response = $this->sendRequest($url, $header, null, 'GET');

            $next = false;
            $httpCode = $response->httpcode ?? 500;

            if ($httpCode == 200) {
                $products = array_merge($products, $response->data);
                $meta = $response->meta_data;

                // si la cantidad de productos es menor que el tamano de la pagina, estamos en el final ..
                if (count($response->data) == $limit) {
                    if ($meta ) {
                        $offset = $page * $limit;
                        $page++;
                        $next = true;
                    }
                }
            } else {
                $error = true;
                break;
            }
        }

        return ($error ? $empty : $products);
    }

    public function getProduct(string $channel, string $idInErp): object
    {
        $product = new stdClass;

        $action = "channels/{$channel}/products/$idInErp/?api_token=" . $this->getApiToken();
        $url = $this->getUrl() . $action;
        $header = $this->getHeader();
        $response = $this->sendRequest($url, $header, null, 'GET');
        $httpCode = $response->httpcode ?? 500;

        if ($httpCode == 200) {
            $product = $response;
        }

        return $product;
    }

    public function deleteProduct(string $id_in_erp, string $channel): bool
    {
        $action = "channels/{$channel}/products/{$id_in_erp}?api_token=" . $this->getApiToken();
        $url = $this->getUrl() . $action;
        $header = $this->getHeader();
        $response = $this->sendRequest($url, $header, null, 'DELETE');
        return ($response->httpcode == 200);
    }
}
