<?php

namespace Application\Controller;


use GoogleGlass\Entity\Timeline\MenuItem;
use GoogleGlass\Entity\Timeline\Attachment;
class TimelineController extends AbstractController
{
    public function viewAction()
    {
        
    }
    
    public function insertAction()
    {
        
        $item = $this->getServiceLocator()->get('GoogleGlass\Timeline\Item');
        
        $request = $this->getRequest();
        $item->setTitle($request->getPost('title', null))
             ->setBundleId($request->getPost('bundleId', null))
             ->setCanonicalUrl($request->getPost('canonicalUrl', null))
             ->setHtml($request->getPost('html', null))
             ->setText($request->getPost('text', null))
             ->setBundleCover((bool)$request->getPost('isBundleCover', false))
             ->setDefaultNotification();
        
        $menuItems = $request->getPost('menuItems', array());
        
        $menuItemArray = array();
        
        foreach($menuItems as $menuItem) {
            $menuItemObj = $this->getServiceLocator()->get('GoogleGlass\Timeline\MenuItem');
            $menuItemObj->setAction($menuItem);
            
            $menuItemArray[] = clone $menuItemObj;
        }
        
        $item->setMenuItems($menuItemArray);
        
        $this->getGlassService()->execute('timeline::insert', $item);
        
        return $this->redirect()->toUrl("/");
    }
    
    protected function generateDemoItem()
    {
        $item = $this->getServiceLocator()->get('GoogleGlass\Timeline\Item');
         
        $item->setText("Hello From Phass!")
        ->setDefaultNotification();
         
        $menuItem = $this->getServiceLocator()->get('GoogleGlass\Timeline\MenuItem');
         
        $menuItem->setId($this->getGlassService()->generateGuid())
        ->setAction(MenuItem::DELETE);
         
        $item->getMenuItems()->append($menuItem);
        
        return $item;
    }
    
    public function demoAction()
    {
        $type = $this->getRequest()->getQuery('type', 'simple');
        
        $item = $this->generateDemoItem();
        
        switch($type) {
            default:
            case 'simple':
                $item->insert();
                break;
            case 'image':
                
                $attachment = $this->getServiceLocator()->get('GoogleGlass\Timeline\Attachment');
                
                $imageFile = APPLICATION_ROOT . '/public/images/saturn-eclipse.jpg';
                
                $attachment->setContent(file_get_contents($imageFile));
                $attachment->setMimeType('image/jpeg');
                
                $item->getAttachments()->append($attachment);
                
                $item->insert();
                break;
                
        }
        
        return $this->redirect()->toUrl("/");
    }
}