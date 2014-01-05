<?php

namespace GoogleGlass\Api;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use GoogleGlass\Api\Exception\InvalidTokenException;
use Zend\Json\Json;
use GoogleGlass\Api\Exception\ApiCallException;
use Zend\EventManager\EventManagerAwareInterface;

abstract class ApiAbstract implements ServiceLocatorAwareInterface, FactoryInterface, EventManagerAwareInterface
{
    use \Zend\ServiceManager\ServiceLocatorAwareTrait;
    use \Zend\EventManager\EventManagerAwareTrait;
    use \GoogleGlass\Log\LoggerTrait;
    
    const GOOGLE_BASE_URI = "https://www.googleapis.com";
    
    /**
     * @var \Zend\Http\Client
     */
    protected $_httpClient;
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $retval = new static();
        
        $client = $serviceLocator->get('GoogleGlass\Http\Client');
        $token = $serviceLocator->get('GoogleGlass\OAuth2\Token');
        
        if(!$token->isValid()) {
            throw new InvalidTokenException("The Token is invalid");
        }
        
        $authHeaders = array(
            'Authorization' => "{$token->getTokenType()} {$token->getAccessToken()}"
        );
        
        $client->setHeaders($authHeaders);
        
        $retval->setHttpClient($client);
        
        return $retval;
    }
    
    protected function executeRequest(\Zend\Http\Client $client)
    {
        $response = $client->send();
        
        try {
            $responseData = Json::decode($response->getBody(), Json::TYPE_ARRAY);
        } catch(\Zend\Json\Exception\RuntimeException $e) {
            file_put_contents("/tmp/response.txt", $response->getBody());
            throw new ApiCallException("JSON Decoding of response failed: {$e->getMessage()}");
        }
        
        if(isset($responseData['error']) && is_array($responseData['error'])) {
        	$errorCode = isset($responseData['error']['code']) ? $responseData['error']['code'] : null;
        	$errorMsg = isset($responseData['error']['message']) ? $responseData['error']['message'] : null;
        
        	throw new ApiCallException($errorMsg, $errorCode);
        }
        
        return $responseData;
    }
    /**
	 * @return the $_httpClient
	 */
	public function getHttpClient() {
		return $this->_httpClient;
	}

	/**
	 * @param \Zend\Http\Client $_httpClient
	 * @return self
	 */
	public function setHttpClient(\Zend\Http\Client $_httpClient) {
		$this->_httpClient = $_httpClient;
		return $this;
	}

	abstract public function execute($data = null);
}