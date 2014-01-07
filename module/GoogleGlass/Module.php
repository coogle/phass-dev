<?php

namespace GoogleGlass;

use Zend\Mvc\MvcEvent;
use GoogleGlass\Api\Exception\InvalidTokenException;
use Zend\Http\Response;

class Module
{
    public function onDispatchError(MvcEvent $e)
    {
        $serviceLocator = $e->getApplication()->getServiceManager();
        $config = $serviceLocator->get('Config');
        $config = $config['googleglass'];
        
        $exception = $e->getParam('exception');
        
        if(!$exception instanceof \Exception)
        {
            return;
        }
        
        $previousException = $exception->getPrevious();
        
        if((!$exception instanceof InvalidTokenException) && (!$previousException instanceof \Exception))
        {
            return;
        }
        
        while($previousException) {
            if($previousException instanceof InvalidTokenException)
            {
                if(is_null($config['auth']['redirect_uri'])) {
                    $router = $serviceLocator->get('Router');
        
                    $requestUri = $router->getRequestUri();
                    $requestUri->setQuery(null);
        
                    $OAuthUrl = $router->assemble(array(), array(
                            'name' => 'googleglass-oauth2-callback',
                            'force_canonical' => true,
                            'uri' => $requestUri
                    ));
                } else {
                    $OAuthUrl = $config['auth']['redirect_uri'];
                }
        
                $response = $e->getResponse();
                $response->setStatusCode(Response::STATUS_CODE_302)
                ->getHeaders()->addHeaderLine('Location', $OAuthUrl);
        
                $e->setResponse($response);
                return;
            }
            $previousException = $previousException->getPrevious();
        }
        
    }
    
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onDispatchError'), 100); 
        
        $sharedManager = $eventManager->getSharedManager();
        $sharedManager->attachAggregate($e->getApplication()->getServiceManager()->get('GoogleGlass\Notifications\Listener'));
    }
    
    public function getAutoloaderConfig()
    {
        return array(
                'Zend\Loader\ClassMapAutoloader' => array(
                        __DIR__ . '/autoload_classmap.php'
                ),
                'Zend\Loader\StandardAutoloader' => array(
                        'namespaces' => array(
                                __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                        )
                )
        );
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'GoogleGlass\Timeline\Item' => 'GoogleGlass\Entity\Timeline\Item',
                'GoogleGlass\Timeline\Attachment' => 'GoogleGlass\Entity\Timeline\Attachment',
                'GoogleGlass\Timeline\MenuItem' => 'GoogleGlass\Entity\Timeline\MenuItem',
                'GoogleGlass\Timeline\NotificationConfig' => 'GoogleGlass\Entity\Timeline\NotificationConfig',
                'GoogleGlass\Contact' => 'GoogleGlass\Entity\Contact',
                'GoogleGlass\Location' => 'GoogleGlass\Entity\Location',
                'GoogleGlass\Timeline' => 'GoogleGlass\Entity\Timeline',
                'GoogleGlass\Service\GlassService' => 'GoogleGlass\Service\GlassServiceFactory',
                'GoogleGlass\Db\Adapter' => 'GoogleGlass\Db\DbFactory',
                'GoogleGlass\Model\CredentialsTable' => 'GoogleGlass\Model\CredentialsTable',
                'GoogleGlass\OAuth\Consumer' => 'GoogleGlass\OAuth\ConsumerFactory',
                'GoogleGlass\Http\Client' => 'GoogleGlass\Http\ClientFactory',
                'GoogleGlass\OAuth2\Client' => 'GoogleGlass\Oauth2\ClientFactory',
                'GoogleGlass\OAuth2\Storage\Session' => 'GoogleGlass\OAuth2\Storage\Session',
                'GoogleGlass\OAuth2\Token' => 'GoogleGlass\OAuth2\TokenFactory',
                'GoogleGlass\Api\Client' => 'GoogleGlass\Api\ClientFactory',
                'GoogleGlass\Subscription' => 'GoogleGlass\Entity\Subscription',
                'GoogleGlass\Notifications\Listener' => 'GoogleGlass\Notifications\NotificationsListenerFactory'
            ),
        );
    }
    
    public function getViewHelperConfig()
    {
        return array(
            'invokables' => array(
                'getItemImageUrl' => 'GoogleGlass\View\Helper\GetItemImageUrl'
            )
        );
    }
}