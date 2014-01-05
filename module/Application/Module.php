<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        $config = $e->getApplication()->getServiceManager()->get('Config');
        
        date_default_timezone_set($config['app']["timezone"]);
        
        $events = $e->getApplication()->getEventManager()->getSharedManager();
        $events->attachAggregate($e->getApplication()->getServiceManager()->get('Application\Listener\GlassListener'));
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getServiceConfig()
    {
    	return array(
			'factories' => array(
                'Application\Listener\GlassListener' => 'Application\Listener\GlassListenerFactory',
			    'Application\Db\Table\Notifications' => 'Application\Db\Table\NotificationTable'
			)
    	);
    }
    
    public function getControllerConfig()
    {
        return array(
            'factories' => array(
                'Application\Controller\Index' => 'Application\Controller\IndexController',
                'Application\Controller\Subscriptions' => 'Application\Controller\SubscriptionsController',
                'Application\Controller\Timeline' => 'Application\Controller\TimelineController',
                'Application\Controller\Contact' => 'Application\Controller\ContactController'
            )
        );
    }
}
