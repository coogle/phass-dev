<?php

namespace GoogleGlass\OAuth2\Storage;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class StorageFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $config = $config['googleglass'];
        
        $tokenStorageObj = $serviceLocator->get($config['tokenStore']);
        
        return $tokenStorageObj;
    }
}