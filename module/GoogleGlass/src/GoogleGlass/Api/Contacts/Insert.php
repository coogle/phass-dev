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
        
        $client = $this->getHttpClient('/mirror/v1/contacts', Request::METHOD_POST);

        $rawPost = $data->toJson(false);
        
        $client->setRawBody($rawPost);
        
        $response = $this->executeRequest($client);
        
        $data->fromJsonResult($response);
        return $data;
    }
}