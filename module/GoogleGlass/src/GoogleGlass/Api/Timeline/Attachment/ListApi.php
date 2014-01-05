<?php

namespace GoogleGlass\Api\Timeline\Attachment;

use GoogleGlass\Api\ApiAbstract;
use Zend\Http\Request;

class ListApi extends ApiAbstract
{
    public function execute($data = null)
    {
        $client = $this->getHttpClient();
        
        $client->setUri("https://www.googleapis.com/mirror/v1/timeline/$data/attachments")->setMethod(Request::METHOD_GET);
        
        $response = $this->executeRequest($client);
        
        $retval = new \ArrayObject($response['items']);
        
        return $retval;
    }
}