<?php

namespace Application\Controller;


use GoogleGlass\Entity\Timeline\MenuItem;
class TimelineController extends AbstractController
{
    public function viewAction()
    {
        
    }
    
    public function demoAction()
    {
        $type = $this->getRequest()->getQuery('type', 'simple');
        
        switch($type) {
        	default:
        	case 'simple':
        	    
        	    $item = $this->getServiceLocator()->get('GoogleGlass\Timeline\Item');
        	    
        	    $item->setText("Hello From Phass!")
        	         ->setDefaultNotification();
        	    
        	    $menuItem = $this->getServiceLocator()->get('GoogleGlass\Timeline\MenuItem');
        	    
        	    $menuItem->setId($this->getGlassService()->generateGuid())
        	             ->setAction(MenuItem::DELETE);
        	    
        	    $item->getMenuItems()->append($menuItem);
        	    
        	    $item->insert();
        	    break;
        	case 'image':
        	    break;
        	    
        }
        
        return $this->redirect()->toUrl("/");
    }
}