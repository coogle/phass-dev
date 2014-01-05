<?php

namespace GoogleGlass\APi\Contacts;

use GoogleGlass\Api\ApiAbstract;
use GoogleGlass\Entity\Contact;
use Zend\Http\Request;

class Delete extends ApiAbstract
{
    public function execute($data = null)
    {
        if($data instanceof Contact) {
            $contactId = $data->getId();
        } else {
            $contactId = (string)$data;
        }
        
        $client = $this->getHttpClient("/mirror/v1/contacts/$contactId", Request::METHOD_DELETE);
        $this->executeRequest($client);
    }
}