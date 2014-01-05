<?php

namespace GoogleGlass\Api\Timeline;

use GoogleGlass\Api\ApiAbstract;
use Zend\Http\Request;

class Get extends ApiAbstract
{
    public function execute($data = null)
    {
        $client = $this->getHttpClient();
        
        $client->setUri('https://www.googleapis.com/mirror/v1/timeline/' . urlencode((string)$data))
               ->setMethod(Request::METHOD_GET);
        
        $responseData = $this->executeRequest($client);
        
        var_dump($responseData);
    }
}