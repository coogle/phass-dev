<?php

namespace Application\Controller;

use GoogleGlass\Controller\AbstractSubscriptionController;

class GlassSubscriptionController extends AbstractSubscriptionController
{
    use \Application\Log\LoggerTrait;
    
    public function onDeleteAction()
    {
        
        $this->logEvent("Got a Delete Notification");
        
        $item = $this->getServiceLocator()->get('GoogleGlass\Timeline\Item');
        
        $this->logEvent("Got Item");
        try {
            $item->setText("How Rude!")
                 ->setDefaultNotification()
                 ->insert();
        } catch(\Exception $e) {
            $this->logEvent("Caught Exception: {$e->getMessage()}");
            $this->logEvent($e->getPrevious()->getMessage());
            
        }
        
        $this->logEvent("Item Inserted");
    }
}