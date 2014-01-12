<?php

namespace GoogleGlass\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GlassClientFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm)
    {
        $config = $sm->get('Config');
        
        $client = new \Google_Client();
        $client->setApplicationName($config['googleglass']['applicationName']);
        
        if(is_null($config['googleglass']['oAuth2Uri'])) {
            $router = $sm->get('Router');
            $requestUri = $router->getRequestUri();
            $requestUri->setQuery(null);
        
            $oAuthUrl = $router->assemble(array(),
                array(
                    'name' => 'googleglass-oauth2-callback',
                    'force_canonical' => true,
                    'uri' => $requestUri)
            );
        } else {
            $oAuthUrl = $config['googleglass']['oAuth2Uri'];
        }
        
        $client->setClientId($config['googleglass']['clientId']);
        $client->setClientSecret($config['googleglass']['clientSecret']);
        $client->setDeveloperKey($config['googleglass']['developerKey']);
        $client->setRedirectUri($oAuthUrl);
        
        $client->setScopes($config['googleglass']['scopes']);
        
        return $client;
    }
}