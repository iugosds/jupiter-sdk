<?php

class jupiter
{


    protected $version = "Jupiter SDK v1.00.01 beta";
    protected $url = "http://astroselling.local/jupiter/v1/";
    protected $token;
    
    
    /**
     * Create a new Jupiter API Client with provided API keys
     *
     * @param string $apiUserName
     * @param string $apiUserKey
     */
    public function __construct(string $apiToken = '')
    {
        $this->token = $apiToken;
    }


    /**
     * Display SDK version
     *
     * @return void
     */    
    public function version() :string
    {
        return $this->version;
    }


    public function getUrl() :string
    {
        return $this->url; 
    }

    public function getApiToken() :string
    {
        return $this->token;
    }


    public function  getChannels() :array
    {
        $channels = array();

        try {
            $action  = "channels?api_token=" . $this->getApiToken();
            $url     = $this->getUrl() . $action; 

            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', $url);
            
            if($response->getStatusCode() == 200) {
                $channels = json_decode($response->getBody());
            }

        } catch (ThrowException $e) {
            $channels = array("error" => $e->getMessage());
        }

        return $channels;
    }


    public function hasChannel(string $channel) :bool
    {
        $exist = false;

        $channels = $this->getChannels();

        if($channels) {
            foreach ($channels as $ch) {
                if($ch->id == $channel) {
                    $exist = true;
                    break;
                }
            }
        }

        return $exist;
    }

    public function createProduct(string $channel, object $product) :bool
    {
        $updated = false;
        $httpCode = 500;

        try {
            
            $id_in_channel = $product->id_in_erp;
            $action = "channels/{$channel}/products?api_token=" . $this->getApiToken();
            $url = $this->getUrl() . $action; 

            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', $url, (array)$product);
           
            $httpCode = $response->getStatusCode();

            if($httpCode == 200) {
                $updated = true;
            }
            
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $httpCode = $response->getStatusCode();
        } catch (ThrowException $e) {
            echo "error ----->" . $e->getMessage();
        }

        return $updated;
    }


    public function updateProduct(string $channel, object $product) :bool
    {
        $updated = false;
        $httpCode = 500;

        try {
            
            $id_in_channel = $product->id_in_erp;
            $action = "channels/{$channel}/products/{$id_in_channel}?api_token=" . $this->getApiToken();
            $url = $this->getUrl() . $action; 

            $client = new \GuzzleHttp\Client();
            $response = $client->request('PUT', $url, (array)$product);
           
            $httpCode = $response->getStatusCode();

            if($httpCode == 200) {
                $updated = true;
            }
            
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $httpCode = $response->getStatusCode();
        } catch (ThrowException $e) {
            echo "error ----->" . $e->getMessage();
        }

        // si no existe el producto, lo mando crear ..
        if($httpCode == 404) {
            $updated = $this->createProduct($channel, $product);
        }

        return $updated;
    }

} // end class