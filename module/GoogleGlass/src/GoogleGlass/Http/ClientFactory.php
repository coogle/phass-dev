<?php

namespace GoogleGlass\Http;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http\Client;

class ClientFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        
        $client = new Client();
        $client->setOptions($config['googleglass']['httpClient']);
        
        return $client;
    }
}