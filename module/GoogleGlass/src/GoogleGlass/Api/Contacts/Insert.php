<?php

namespace GoogleGlass\Api\Contacts;

use GoogleGlass\Api\ApiAbstract;
use GoogleGlass\Entity\Contact;
use Zend\Http\Request;

class Insert extends ApiAbstract
{
    public function execute($data = null)
    {
        if(!$data instanceof Contact) {
            throw new \InvalidArgumentException("Must provide a Contact object");
        }
        
        $client = $this->getHttpClient()
                       ->setUri("https://www.googleapis.com/mirror/v1/contacts")
                       ->setMethod(Request::METHOD_POST);
        
        $client->getRequest()
               ->getHeaders()
               ->addHeaderLine('Content-Type', 'application/json');
        
        $rawPost = $data->toJson(false);
        
        $client->setRawBody($rawPost);
        
        $response = $this->executeRequest($client);
        
        $data->fromJsonResult($response);
        return $data;
    }
}