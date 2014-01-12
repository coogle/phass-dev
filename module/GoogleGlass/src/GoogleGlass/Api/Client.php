<?php

namespace GoogleGlass\Api;

use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ServiceManager\FactoryInterface;
use GoogleGlass\Api\Exception\ApiCallNotFoundException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\Log\Logger;

class Client implements ServiceProviderInterface, FactoryInterface
{
    use \Zend\ServiceManager\ServiceLocatorAwareTrait;
    use \GoogleGlass\Entity\SimpleFactoryTrait;
    use \GoogleGlass\Log\LoggerTrait;
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'timeline::insert' => 'GoogleGlass\Api\Timeline\Insert',
                'timeline::get' => 'GoogleGlass\Api\Timeline\Get',
                'timeline::list' => 'GoogleGlass\Api\Timeline\ListApi',
                
                'timeline::attachment::get' => 'GoogleGlass\Api\Timeline\Attachment\Get',
                'timeline::attachment::list' => 'GoogleGlass\Api\Timeline\Attachment\ListApi',
                
                'subscriptions::list' => 'GoogleGlass\Api\Subscriptions\ListApi',
                'subscriptions::delete' => 'GoogleGlass\Api\Subscriptions\Delete',
                'subscriptions::insert' => 'GoogleGlass\Api\Subscriptions\Insert',
                
                'contacts::list' => 'GoogleGlass\Api\Contacts\ListApi',
                'contacts::delete' => 'GoogleGlass\Api\Contacts\Delete',
                'contacts::insert' => 'GoogleGlass\Api\Contacts\Insert'
            )
        );
    }
    
    public function execute($apiCall, $data = null)
    {
        try {
            $apiObject = $this->getServiceLocator()->get($apiCall);
            $apiObject->setServiceLocator($this->getServiceLocator());
        } catch(ServiceNotFoundException $e) {
            throw new ApiCallNotFoundException("The API Call '$apiCall' was not found");
        }
        
        if(!$apiObject instanceof ApiAbstract) {
            throw new ApiCallNotFoundException("The API Call is not valid");
        }
        
        try {
            $this->logEvent("Executing API call '$apiCall'", Logger::DEBUG);
            
            return $apiObject->execute($data);
        } catch(\Exception $e) {
            $this->logEvent("Exception caught trying to execute API call '$apiCall': {$e->getMessage()}", Logger::ERR);
            throw $e;
        }
    }
}