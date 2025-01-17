<?php

namespace GoogleGlass\Api\Timeline\Attachment;

use GoogleGlass\Api\ApiAbstract;
use Zend\Http\Request;

class ListApi extends ApiAbstract
{
    public function execute($data = null)
    {
        $client = $this->getHttpClient("/mirror/v1/timeline/$data/attachments", Request::METHOD_GET);
        
        $response = $this->executeRequest($client);
        
        $retval = new \ArrayObject($response['items']);
        
        return $retval;
    }
}