<?php

namespace Application\Controller;

use Zend\View\Model\ViewModel;

class SubscriptionsController extends AbstractController
{
    public function indexAction()
    {
        $subscriptions = $this->getGlassService()->execute('subscriptions::list');
        
        return new ViewModel(compact('subscriptions'));
    }
    
    public function unsubscribeAction()
    {
       $id = $this->getRequest()->getQuery('id', false);
       
       if(!$id) {
           throw new \InvalidArgumentException("You must provide an ID to delete");
       }
       
       $result = $this->getGlassService()->unsubscribe($id);
       
       return $this->redirect()->toUrl('/subscriptions');
    }
    
    public function subscribeAction()
    {
        $ops = $this->getRequest()->getQuery('op', array());
        $id = $this->getRequest()->getQuery('id', false);
        
        if(!$id) {
            throw new \InvalidArgumentException("Subscription ID required");
        }
        
        $result = $this->getGlassService()->subscribe($id, $ops);
        
        return $this->redirect()->toUrl('/subscriptions');
    }
}