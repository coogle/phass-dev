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
        
        $client = $this->getHttpClient()
                       ->setUri('https://www.googleapis.com/mirror/v1/subscriptions')
                       ->setMethod(Request::METHOD_POST);
        
        $client->setRawBody($data->toJson());
        
        $client->getRequest()
               ->getHeaders()
               ->addHeaderLine('Content-Type', 'application/json');
        
        $response = $this->executeRequest($client);
        
        $retval = $this->getServiceLocator()->get('GoogleGlass\Subscription');
        $retval->fromJsonResult($response);
        
        return $retval;
    }
}