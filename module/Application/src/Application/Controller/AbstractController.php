<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\EventManager\Event;
use Zend\Mvc\MvcEvent;

abstract class AbstractController extends AbstractActionController implements FactoryInterface 
{
    use \Application\Log\LoggerTrait;
    
    public function createService(ServiceLocatorInterface $controllerServiceLocator)
    {
        $controller = new static();
        $serviceLocator = $controllerServiceLocator->getServiceLocator();
        $events = $serviceLocator->get('EventManager');
        
        $events->attach(MvcEvent::EVENT_DISPATCH, function(Event $e) use($controller) {
            
            if(!$controller->getGlassService()->isAuthenticated())
            {
                return $controller->redirect()->toRoute('googleglass-oauth2-callback');
            }
            
        }, 100); // Execute before executing action
        
        $glassService = $serviceLocator->get('GoogleGlass\Service\GlassService');
        
        $controller->setGlassService($glassService);
        $controller->setEventManager($events);
        
        return $controller;
    }
    
    /**
     * @var \GoogleGlass\Service\GlassService
     */
    protected $_glassService;
    
	/**
	 * @return the $_glassService
	 */
	public function getGlassService() {
		return $this->_glassService;
	}

	/**
	 * @param \GoogleGlass\Service\GlassService $_glassService
	 * @return self
	 */
	public function setGlassService(\GoogleGlass\Service\GlassService $_glassService) {
		$this->_glassService = $_glassService;
		return $this;
	}

    
}