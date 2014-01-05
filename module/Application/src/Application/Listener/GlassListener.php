<?php

namespace Application\Listener;

use GoogleGlass\Events as GlassEvent;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\SharedListenerAggregateInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class GlassListener implements SharedListenerAggregateInterface, EventManagerAwareInterface, ServiceLocatorAwareInterface
{
    use \Zend\EventManager\EventManagerAwareTrait;
    use \Zend\ServiceManager\ServiceLocatorAwareTrait;
    use \Application\Log\LoggerTrait;
    
    protected $_listeners = array();
    
    public function attachShared(SharedEventManagerInterface $events)
    {
        $self = $this;
        $attachEvent = function($glassEvent, $method) use ($events, $self)
        {
            $service = 'GoogleGlass\Service\GlassService';
            $this->_listeners[] = $events->attach($service, $glassEvent, array($self, $method));
        };
        
        $attachEvent(GlassEvent::EVENT_SUBSCRIPTION_SHARE, 'onShareNotification');
        $attachEvent(GlassEvent::EVENT_SUBSCRIPTION_LOCATION, 'onLocationNotification');
    }
    
    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach($this->_listeners as $key => $val)
        {
            if($events->detach($val)) {
                unset($this->_listeners[$key]);
            }
        }
    }
    
    public function onShareNotification(Event $e)
    {
        $this->logEvent("Got Share Notification!");
        
        $notification = $e->getParam('notification', null);
        
        if(is_null($notification)) {
            return;
        } 
       
       $notificationTable = $this->getServiceLocator()->get('Application\Db\Table\Notification');
       
       $notification = $notificationTable->create();
       
       $notification->setItemId($notification->getItemId())
                    ->setUserToken($notification->getUserToken())
                    ->setCreated(new \DateTime('now'))
                    ->setOperation($notification->getOperation())
                    ->setCollection('timeline');
       
       $notificationTable->save($notification);
    }
    
    public function onLocationNotification(Event $e)
    {
        $this->logEvent("Got Location Update!");
        
    }
}
