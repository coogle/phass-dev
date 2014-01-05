<?php

namespace GoogleGlass\Api\Timeline;

use GoogleGlass\Api\ApiAbstract;
use GoogleGlass\Entity\Timeline\Item;
use Zend\Http\Request;

class Insert extends ApiAbstract
{
    public function execute($data = null)
    {
        if(!$data instanceof Item) {
            throw new \InvalidArgumentException("Must provide a Timeline Item");
        }
        
        $itemAttachments = $data->getAttachments();
        
        if($itemAttachments->count() > 0) {
            return $this->executeWithAttachments($data);
        }
        
        $client = $this->getHttpClient('/mirror/v1/timeline', Request::METHOD_POST);
        
        $rawPost = $data->toJson(false);
        
        $client->setRawBody($rawPost);
        
        $response = $this->executeRequest($client);
        
        $data->fromJsonResult($response);
        
        return $data;
    }
}