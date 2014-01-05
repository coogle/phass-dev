<?php

namespace GoogleGlass\Entity\Subscription\Notification;

trait GetItemTrait
{
    /**
     * @var string
     */
    protected $_itemId;
    
    public function setItemId($id) {
        $this->_itemId = $id;
        return $this;
    }
    
    public function getItemId() {
        return $this->_itemId;
    }
    
    /**
     * @return Google_Service_Mirror_TimelineItem
     */
    public function getItem()
    {
        $itemId = $this->getItemId();
        
        $glassService = $this->getServiceLocator()->get('GoogleGlass\Service\GlassService');
        return $glassService->timeline()->get($itemId);
    }
}