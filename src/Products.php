<?php

namespace astroselling\Jupiter;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use stdClass;
use GuzzleHttp\Client;

class Products
{
    protected string $token;
    private Client $client;

    public function __construct(string $url = '', string $apiToken = '')
    {
        $this->token = $apiToken;
        $this->client = new Client(['base_uri' => $url, 'headers' => ['Accept' => 'application/json']]);
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

    public function getChannels(): object
    {
        $action = "channels?api_token=" . $this->token;
        $response = $this->client->get($action);
        return $this->formatResponse($response);
    }

    /**
     * @throws GuzzleException
     */
    public function updateProduct(string $channel, object $product): bool
    {
        $action = "channels/{$channel}/products/{$product->id_in_erp}?api_token=" . $this->token;
        $response = $this->client->put($action, ['json' => $product]);

        if ($response->getStatusCode() == 404) {
            $this->createProduct($channel, $product);
        }

        return true;
    }

    /**
     * @throws GuzzleException
     */
    public function createProduct(string $channel, object $product): bool
    {
        $action = "channels/{$channel}/products?api_token=" . $this->token;
        $this->client->post($action, ['json' => $product]);
        return true;
    }

    /**
     * @throws GuzzleException
     */
    public function getProducts(string $channel, int $limit = 500): array
    {
        $products = [];
        $next = true;
        $page = 1;
        $offset = 0;

        while ($next) {
            $action = "channels/{$channel}/products?api_token=" . $this->token . "&limit={$limit}&offset={$offset}";
            $response = $this->client->get($action);
            $response = $this->formatResponse($response);
            $next = false;
            $products = array_merge($products, $response->data);
            $meta = $response->meta_data;

            // si la cantidad de productos es menor que el tamano de la pagina, estamos en el final ..
            if (count($response->data) == $limit) {
                if ($meta) {
                    $offset = $page * $limit;
                    $page++;
                    $next = true;
                }
            }
        }

        return $products;
    }

    /**
     * @throws GuzzleException
     */
    public function getProduct(string $channel, string $idInErp): object
    {
        $action = "channels/{$channel}/products/$idInErp/?api_token=" . $this->token;
        $response = $this->client->get($action);
        return $this->formatResponse($response);
    }

    /**
     * @throws GuzzleException
     */
    public function deleteProduct(string $id_in_erp, string $channel): bool
    {
        $action = "channels/{$channel}/products/{$id_in_erp}?api_token=" . $this->token;
        $this->client->delete($action);
        return true;
    }

    private function formatResponse(ResponseInterface $response): object
    {
        $result = new stdClass();
        $responseBody = $response->getBody()->getContents();
        $decodedResponse = json_decode($responseBody);

        if (!is_object($decodedResponse)) {
            $result->data = (object)$decodedResponse;
        } else {
            $result = $decodedResponse;
        }

        $result->httpcode = $response->getStatusCode();

        return $result;
    }
}
