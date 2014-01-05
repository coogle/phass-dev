<?php

namespace GoogleGlass\Api\Subscriptions;

use GoogleGlass\Api\ApiAbstract;
use GoogleGlass\Entity\Subscription;
use Zend\Http\Request;

class Insert extends ApiAbstract
{
    public function execute($data = null)
    {
        if(!$data instanceof Subscription)
        {
            throw new \InvalidArgumentException("Must provide a subscription entity");
        }
        
        $client = $this->getHttpClient('/mirror/v1/subscriptions', Request::METHOD_POST);
        
        $client->setRawBody($data->toJson());
        
        $response = $this->executeRequest($client);
        
        $retval = $this->getServiceLocator()->get('GoogleGlass\Subscription');
        $retval->fromJsonResult($response);
        
        return $retval;
    }
}