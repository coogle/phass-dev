<?php

namespace GoogleGlass\OAuth2\Storage;

use GoogleGlass\Entity\OAuth2\Token;
use Zend\Session\Container;
use GoogleGlass\OAuth2\Storage\StorageInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class Session implements StorageInterface, FactoryInterface, ServiceLocatorAwareInterface
{
    const CONTAINER_NAME = "GoogleGlass";
    
    use \Zend\ServiceManager\ServiceLocatorAwareTrait;
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new self();
    }
    
    public function store(Token $token)
    {
        $container = new Container(static::CONTAINER_NAME);
        $container->token = $token;
    }
    
    public function retrieve()
    {
        $container = new Container(static::CONTAINER_NAME);
        
        if($container->token instanceof Token) {
            return $container->token;
        }
        
        return null;
    }
    
    public function destroy()
    {
        $container = new Container(static::CONTAINER_NAME);
        unset($container->token);
    }
}