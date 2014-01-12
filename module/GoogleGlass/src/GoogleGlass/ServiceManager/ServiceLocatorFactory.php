<?php

namespace GoogleGlass\ServiceManager;

use Zend\ServiceManager\ServiceLocatorInterface;
class ServiceLocatorFactory
{
    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    static protected $_serviceLocator;
    
    static public function getInstance()
    {
        if(is_null(static::$_serviceLocator)) {
            throw new \RuntimeException("Service Locator Not Set");
        }
        
        return static::$_serviceLocator;
    }
    
    static public function setInstance(ServiceLocatorInterface $serviceLocator)
    {
        static::$_serviceLocator = $serviceLocator;
    }
}