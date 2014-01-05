<?php

namespace GoogleGlass\Api\Subscriptions;

use GoogleGlass\Api\ApiAbstract;
use Zend\Http\Request;

class Delete extends ApiAbstract
{
    public function execute($data = null)
    {
        $client = $this->getHttpClient()
                       ->setUri('https://www.googleapis.com/mirror/v1/subscriptions/' . (string)$data)
                       ->setMethod(Request::METHOD_DELETE);
        
        $response = $this->executeRequest($client);
        
        var_dump($response);
    }
}