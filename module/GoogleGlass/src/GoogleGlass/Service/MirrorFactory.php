<?php

namespace GoogleGlass\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

class MirrorFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm)
    {
        $client = $sm->get('GoogleGlass\Service\GlassClient');
        $container = new Container('GoogleGlass');
        $accessToken = $container->accessToken;
        
        if(empty($accessToken)) {
        	return null;
        }
        
        $client->setAccessToken($accessToken);
        
        $mirror = new \Google_Service_Mirror($client);
        
        return $mirror;
    }
}