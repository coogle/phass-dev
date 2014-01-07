<?php

namespace GoogleGlass\Notifications;

use GoogleGlass\Events as GlassEvent;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\SharedListenerAggregateInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\MvcEvent;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;

class NotificationsListener implements SharedListenerAggregateInterface, EventManagerAwareInterface, ServiceLocatorAwareInterface
{
    use \Zend\EventManager\EventManagerAwareTrait;
    use \Zend\ServiceManager\ServiceLocatorAwareTrait;
    use \GoogleGlass\Log\LoggerTrait;
    
    protected $_listeners = array();
    
    public function attachShared(SharedEventManagerInterface $events)
    {
        $self = $this;
        $attachEvent = function($glassEvent, $method) use ($events, $self)
        {
            $service = 'GoogleGlass\Service\GlassService';
            $this->_listeners[] = $events->attach($service, $glassEvent, array($self, $method), -100);
        };
        
        $attachEvent(GlassEvent::EVENT_SUBSCRIPTION_CUSTOM, 'onSubscriptionEvent');
        $attachEvent(GlassEvent::EVENT_SUBSCRIPTION_DELETE, 'onSubscriptionEvent');
        $attachEvent(GlassEvent::EVENT_SUBSCRIPTION_LAUNCH, 'onSubscriptionEvent');
        $attachEvent(GlassEvent::EVENT_SUBSCRIPTION_LOCATION, 'onSubscriptionEvent');
        $attachEvent(GlassEvent::EVENT_SUBSCRIPTION_REPLY, 'onSubscriptionEvent');
        $attachEvent(GlassEvent::EVENT_SUBSCRIPTION_SHARE, 'onSubscriptionEvent');
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
    
    public function onSubscriptionEvent(Event $e)
    {
        $this->logEvent("Got Subscription Notification!");
        
        $config = $this->getServiceLocator()->get('Config');
        
        if(is_null($config['googleglass']['subscriptionController'])) {
            throw new \RuntimeException("Subscription Controller Not Specified in Config");
        }
        
        $application = $this->getServiceLocator()->get('Application');
        $eventManager = $application->getEventManager();
        
        $mvcEvent = new MvcEvent();
        $mvcEvent->setTarget($application)
                 ->setApplication($application)
                 ->setRequest(new Request())
                 ->setResponse(new Response())
                 ->setRouter($this->getServiceLocator()->get('Router'));
        
        switch($e->getName()) {
            case GlassEvent::EVENT_SUBSCRIPTION_CUSTOM:
                $action = 'onCustom';
                break;
            case GlassEvent::EVENT_SUBSCRIPTION_DELETE:
                $action = 'onDelete';
                break;
            case GlassEvent::EVENT_SUBSCRIPTION_LAUNCH:
                $action = 'onLaunch';
                break;
            case GlassEvent::EVENT_SUBSCRIPTION_LOCATION:
                $action = "onLocation";
                break;
            case GlassEvent::EVENT_SUBSCRIPTION_REPLY:
                $action = "onReply";
                break;
            case GlassEvent::EVENT_SUBSCRIPTION_SHARE:
                $action = "onShare";
                break;
            default:
                throw new \RuntimeException("Unknown Event");
        }
        
        $matches = new RouteMatch(array(
            'controller' => $config['googleglass']['subscriptionController'],
            'action' => $action
        ));
        

        $mvcEvent->setRouteMatch($matches);
                
        $result = $eventManager->trigger(MvcEvent::EVENT_DISPATCH, $mvcEvent);
        
        $response = $result->first();
        
        /**
         * @todo Something useful with $response
         */
    }
}
