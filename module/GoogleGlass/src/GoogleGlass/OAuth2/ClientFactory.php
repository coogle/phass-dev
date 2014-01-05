<?php

namespace GoogleGlass\OAuth2;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ClientFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $httpClient = $serviceLocator->get('GoogleGlass\Http\Client');
        $config = $serviceLocator->get('Config');
        $config = $config['googleglass'];
        
        if(is_null($config['auth']['redirect_uri'])) {
        	$router = $serviceLocator->get('Router');
        
        	$requestUri = $router->getRequestUri();
        	$requestUri->setQuery(null);
        
        	$OAuthUrl = $router->assemble(array(), array(
        			'name' => 'googleglass-oauth2-callback',
        			'force_canonical' => true,
        			'uri' => $requestUri
        	));
        
        } else {
        	$OAuthUrl = $config['auth']['callbackUri'];
        }
        
        $postParams = array(
            'client_id' => $config['auth']['client_id'],
            'client_secret' => $config['auth']['client_secret'],
            'redirect_uri' => $OAuthUrl,
            'grant_type' => 'authorization_code'
        );
        
        $httpClient->setMethod(\Zend\Http\Request::METHOD_POST)
                   ->setUri($config['auth']['token_uri'])
                   ->setParameterPost($postParams);
        
        return $httpClient;
    }
}