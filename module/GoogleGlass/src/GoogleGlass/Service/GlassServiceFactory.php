<?php

namespace GoogleGlass\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GlassServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm)
    {
        $client = $sm->get('GoogleGlass\Api\Client');
        $service = new GlassService();
        $service->setGlassApiClient($client);
        
        return $service;
    }
}