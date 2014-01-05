<?php

namespace GoogleGlass\Api\Contacts;

use GoogleGlass\Api\ApiAbstract;
use Zend\Http\Request;

class ListApi extends ApiAbstract
{
    public function execute($data = null)
    {
        $client = $this->getHttpClient()
                       ->setUri('https://www.googleapis.com/mirror/v1/contacts')
                       ->setMethod(Request::METHOD_GET);
        
        $response = $this->executeRequest($client);
        
        $list = new \ArrayObject();
        
        foreach($response['items'] as $contact) {
            $contactObj = $this->getServiceLocator()->get('GoogleGlass\Contact');
            $contactObj->fromJsonResult($contact);
            $list[] = clone $contactObj;
        }
         
        return $list;
    }
}