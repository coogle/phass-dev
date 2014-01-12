<?php

namespace GoogleGlass;

use Zend\Mvc\MvcEvent;
use GoogleGlass\Api\Exception\InvalidTokenException;
use Zend\Http\Response;
use GoogleGlass\ServiceManager\ServiceLocatorFactory;

class Module
{
    /**
     * This method listens for a Dispatching error and checks to see if
     * the error was caused by us not having a valid token. If it is, we auto
     * magically redirect the user into the OAuth2 workflow to reauth.
     * 
     * @param MvcEvent $e
     */
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
        
        ServiceLocatorFactory::setInstance($e->getApplication()->getServiceManager());
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
                'GoogleGlass\OAuth2\Client' => 'GoogleGlass\OAuth2\ClientFactory',
                'GoogleGlass\OAuth2\Storage\Session' => 'GoogleGlass\OAuth2\Storage\Session',
                'GoogleGlass\OAuth2\TokenStore' => 'GoogleGlass\OAuth2\Storage\StorageFactory',
                'GoogleGlass\OAuth2\Token' => 'GoogleGlass\OAuth2\TokenFactory',
                'GoogleGlass\OAuth2\Jwt' => 'GoogleGlass\OAuth2\Jwt\Jwt',
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