<?php

namespace GoogleGlass\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Log\Logger;
use GoogleGlass\Events;
use Zend\Loader\Exception\SecurityException;
use GoogleGlass\Entity\Subscription\Notification\AbstractNotification;
use GoogleGlass\Service\GlassService;
use Zend\Uri\UriFactory;
use Zend\Json\Json;
use GoogleGlass\Entity\OAuth2\Token;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

class CallbackController extends AbstractActionController
{
    use \GoogleGlass\Log\LoggerTrait;
    
    public function setEventManager(\Zend\EventManager\EventManagerInterface $events)
    {
        parent::setEventManager($events);
        $events->addIdentifiers(GlassService::EVENT_IDENTIFIER);
        return $this;
    }
    
    public function subscriptionCallbackAction()
    {
        $this->logEvent("Recieved ping on Glass subscription endpoint");
        
        file_put_contents("/tmp/request.txt", var_export($this->getRequest()->__toString(), true));
        $glassService = $this->getServiceLocator()->get('GoogleGlass\Service\GlassService');
        
        $notification = $this->params()->fromJson();
        
        if($notification === false) {
            $this->logEvent("Invalid Subscription Notification", 'error');
            throw new \InvalidArgumentException("Invalid Subscription Notification");
        }
        
        if(!isset($notification['verifyToken'])) {
            $this->logEvent('Verify Token Not Present', 'error');
            throw new SecurityException("Verify Token not present");
        }
        
        if($notification['verifyToken'] !== $glassService->generateVerifyToken()) {
            $this->logEvent('Verify Token is invalid');
            throw new SecurityException("Verify Token is invalid");
        }
        
        switch($notification['collection']) {
            case GlassService::COLLECTION_LOCATIONS:
                $obj = AbstractNotification::getInstanceFromArray($notification, $this->getServiceLocator());
                
                $this->getEventManager()->trigger(Events::EVENT_SUBSCRIPTION_LOCATION, null, array('notification' => $obj));
                break;
            case GlassService::COLLECTION_TIMELINE:
                
                $this->logEvent("Received Timeline Subscription Notification");
                
                foreach($notification['userActions'] as $action) {
                    if(!isset($action['type'])) {
                        throw new \InvalidArgumentException("Missing Action Type");
                    }
                    
                    $obj = AbstractNotification::getInstanceFromArray($notification, $this->getServiceLocator(),  $action['type']);
                    
                    switch($action['type']) {
                        case GlassService::ACTION_TYPE_CUSTOM:
                            $event = Events::EVENT_SUBSCRIPTION_CUSTOM;
                            break;
                        case GlassService::ACTION_TYPE_DELETE:
                            $event = Events::EVENT_SUBSCRIPTION_DELETE;
                            break;
                        case GlassService::ACTION_TYPE_LAUNCH:
                            $event = Events::EVENT_SUBSCRIPTION_LAUNCH;
                            break;
                        case GlassService::ACTION_TYPE_REPLY:
                            $event = Events::EVENT_SUBSCRIPTION_REPLY;
                            break;
                        case GlassService::ACTION_TYPE_SHARE:
                            $event = Events::EVENT_SUBSCRIPTION_SHARE;
                            break;
                        default:
                            throw new \InvalidArgumentException("Invalid Action Type");
                    }
                    
                    $this->getEventManager()->trigger($event, null, array('notification' => $obj));
                }
                break;
            default:
                throw new \InvalidArgumentException("Invalid Collection Provided");
        }
        
        $response = $this->getResponse();
        $response->setStatusCode(\Zend\Http\Response::STATUS_CODE_200);
        $response->setContent(null);
        
        $this->glass()->sendResponse($response);
    }
    
    protected function doRefreshToken(Token $token)
    {
        $oAuthClient = $this->getServiceLocator()->get('GoogleGlass\OAuth2\Client');
        $params = $oAuthClient->getRequest()->getPost()->toArray();
        
        if(is_null($token->getRefreshToken())) {
            return $this->doRequestToken();
        }
        
        $postParams = array(
                'refresh_token' => $token->getRefreshToken(),
                'client_id' => $params['client_id'],
                'client_secret' => $params['client_secret'],
                'grant_type' => 'refresh_token'
        );
        
        $oAuthClient->setParameterPost($postParams);
        
        $response = $oAuthClient->send();
        $tokenData = Json::decode($response->getBody(), Json::TYPE_ARRAY);
        
        $this->processOauth2Token($tokenData);
        
        return $this->redirect()->toUrl('/');
    }
    
    protected function doRequestToken()
    {
        $config = $this->getServiceLocator()->get('Config');
        $config = $config['googleglass'];
        
        if(is_null($config['auth']['redirect_uri'])) {
            $router = $this->getServiceLocator()->get('Router');
        
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
        
        $uri = UriFactory::factory($config['auth']['auth_uri']);
        $uri->setQuery(array(
            'response_type' => 'code',
            'client_id' => $config['auth']['client_id'],
            'redirect_uri' => $OAuthUrl,
            'scope' => implode(' ', $config['auth']['scopes']),
            'state' => '',
            'access_type' => 'offline',
            'approval_prompt' => 'auto',
            'include_granted_scopes' => 'true'
        ));
        
        return $this->redirect()->toUrl($uri->__toString());
    }
    
    public function OAuth2CallbackAction()
    {
        $config = $this->getServiceLocator()->get('Config');
        $config = $config['googleglass'];
        
        switch(true) {
            case is_null($config['auth']['client_id']):
            case is_null($config['auth']['client_secret']):
                throw new \RuntimeException("You must configure this module"); 
        }
        
        $tokenStorageObj = $this->getServiceLocator()->get($config['tokenStore']);
        
        if(!$tokenStorageObj instanceof \GoogleGlass\OAuth2\Storage\StorageInterface) {
            throw new ServiceNotFoundException("Provided storage service must implement StorageInterface");
        }
        
        if(empty($_GET)) {
            
            $token = $tokenStorageObj->retrieve();
            
            if($token instanceof Token)
            {
                $this->logEvent("Refreshing existing token");
                return $this->doRefreshToken($token);
            }
            
            $this->logEvent("Requesting new Access Token for user");
            return $this->doRequestToken();
        }
        
        if($this->getRequest()->getQuery('error', false)) {
             $this->logEvent("Error authenticating with Google: {$this->getRequest()->getQuery('error')}", Logger::ERR);
             throw new \RuntimeException("Error Authenticating with Google");
        }
        
        $code = $this->getRequest()->getQuery('code', false);
        
        if(!$code) {
            $this->logEvent("Did not receive code from Google as expected during OAuth2", Logger::ERR);
            throw new \RuntimeException("Did not receive code as expected");
        }
        
        $oAuthClient = $this->getServiceLocator()->get('GoogleGlass\OAuth2\Client');
        $oAuthClient->getRequest()->getPost()->set('code', $code);
        
        $response = $oAuthClient->send();
        
        $tokenData = Json::decode($response->getBody(), Json::TYPE_ARRAY);
        
        $this->processOauth2Token($tokenData);
        
        return $this->redirect()->toUrl('/');
    }
    
    protected function processOauth2Token(array $tokenData)
    {
        $tokenStorageObj = $this->getServiceLocator()->get('GoogleGlass\Oauth2\TokenStore');
        
        if(!$tokenStorageObj instanceof \GoogleGlass\OAuth2\Storage\StorageInterface) {
            throw new ServiceNotFoundException("Provided storage service must implement StorageInterface");
        }
        
        if(isset($tokenData['error'])) {
            $this->logEvent("Failed to get access token from OAuth2: {$tokenData['error']}", Logger::ERR);
            throw new \RuntimeException("Failed to get access token");
        }
        
        $token = clone $this->getServiceLocator()->get('GoogleGlass\OAuth2\Token');
        
        $tokenStorageObj->store($token->fromApiResult($tokenData));
        
        $this->getEventManager()->trigger(Events::EVENT_NEW_AUTH_TOKEN, null, array('token' => $token));
        
    }
    
    public function unauthAction()
    {
        $tokenStorageObj = $this->getServiceLocator()->get('GoogleGlass\OAuth2\TokenStore');
        $tokenStorageObj->destroy();
        
        return $this->redirect()->toUrl('/');
    }
    
}