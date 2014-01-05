<?php

namespace Application\Controller;

use Zend\View\Model\ViewModel;

class ContactController extends AbstractController
{
    public function indexAction()
    {
        $client = $this->getServiceLocator()->get('GoogleGlass\Service\GlassService');
        $contacts = $client->execute('contacts::list');
        
        return new ViewModel(compact('contacts'));
    }
}