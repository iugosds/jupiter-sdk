<?php

namespace astroselling\Jupiter;

class Products
{
    protected $version = "Jupiter SDK v1.05";
    protected $url;
    protected $token;

    
    /**
     * Create a new Jupiter API Client with provided API keys
     *
     * @param string $apiUserName
     * @param string $apiUserKey
     */
    public function __construct(string $url = '', string $apiToken = '')
    {
        $this->url = $url;
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

    public function sendRequest($url, $header = '', $content = '', $type = 'POST', $xml = false)  :object
    {       
        ini_set('max_execution_time', 3000); 
        ini_set('memory_limit', '1024M');
        
        $result = new \stdClass();
                
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        if($content) {          
            if(!$xml) {
                $fields = json_encode($content, JSON_UNESCAPED_UNICODE);
            }   
            else {
                curl_setopt($curl,CURLOPT_POST, count($content));
                $fields = http_build_query($content);
            }
            
            curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
        }
        
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $type);

        if($xml) {
            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_MAXREDIRS, 10);          
        }

        if($header) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }   
            
        $response = curl_exec($curl);
        $error    = curl_error($curl);
            
        if($error || !$response) {     
            $result = new \stdClass();       
            $result->error = $error;
        }
        else {
            $curlResponse = json_decode($response);
            //print_r($curlResponse);
            if(!is_object($curlResponse)) {
                $result->data = (object) $curlResponse;
            }         
            else {
                $result = $curlResponse;
            }  
        }

        // keep http code
        $result->httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        curl_close($curl);

        return $result;
    }

    public function getHeader() : array
    {

        return array(
                    "Cache-Control: no-cache",
                    "Content-Type: application/json"
                    );
    }
    

    public function  getChannels() 
    {
        $channels = array();

        try {
            $action = "channels?api_token=" . $this->getApiToken();
            $url = $this->getUrl() . $action; 

            $header = $this->getHeader();
            $content = array();
            $channels = $this->sendRequest($url, $header, $content, 'GET');
            
            $httpCode = $channels->httpcode ?? 500;       
            if($httpCode == 200) {
                $updated = true;
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

        if(isset($channels->data)) {
            foreach ($channels->data as $ch) {
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
            $header = $this->getHeader();
            $response = $this->sendRequest($url, $header, $product, 'POST');
           
            $httpCode = $response->httpcode ?? 500;       
            if($httpCode == 200) {
                $updated = true;
            }
            
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
            $header = $this->getHeader();
            $response = $this->sendRequest($url, $header, $product, 'PUT');          
            
            $httpCode = $response->httpcode ?? 500;            
            if($httpCode == 200) {
                $updated = true;
            }
            
        } catch (ThrowException $e) {
            echo "error ----->" . $e->getMessage();
        }

        // si no existe el producto, lo mando crear ..
        if($httpCode == 404) {
            $updated = $this->createProduct($channel, $product);
        }

        return $updated;
    }

    public function getProducts(string $channel) :array
    {
        $products = array();
        $httpCode = 500;

        try {
            
            $action = "channels/{$channel}/products?api_token=" . $this->getApiToken();
            $url = $this->getUrl() . $action; 
            $header = $this->getHeader();
            $content = array();
            $response = $this->sendRequest($url, $header, $content, 'GET');          
            
            $httpCode = $response->httpcode ?? 500;            
            if($httpCode == 200) {               
                $products = $response->data;
            }
            
        } catch (ThrowException $e) {
            echo "error ----->" . $e->getMessage();
        }

        return $products;
    }
} // end class