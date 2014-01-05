<?php

namespace GoogleGlass\OAuth2;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use GoogleGlass\OAuth\Storage\StorageInterface;
use Guzzle\Service\Exception\ServiceNotFoundException;
use GoogleGlass\Entity\OAuth2\Token;

class TokenFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        
        $tokenStorageObj = $serviceLocator->get($config['googleglass']['tokenStore']);
        
        if(!$tokenStorageObj instanceof \GoogleGlass\OAuth2\Storage\StorageInterface) {
            throw new ServiceNotFoundException("Provided storage service must implement StorageInterface");
        }
        
        $token = $tokenStorageObj->retrieve();
        
        if(!$token instanceof Token) {
            $token = new Token();
        }
        
        return $token;
    }
}