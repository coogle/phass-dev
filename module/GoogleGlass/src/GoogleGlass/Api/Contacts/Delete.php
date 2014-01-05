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
        
        $client = $this->getHttpClient()
                       ->setUri("https://www.googleapis.com/mirror/v1/contacts/$contactId")
                       ->setMethod(Request::METHOD_DELETE);
        
        return $this->executeRequest($client);
    }
}