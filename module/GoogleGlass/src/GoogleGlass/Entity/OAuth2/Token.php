<?php

namespace GoogleGlass\Entity\OAuth2;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use GoogleGlass\ServiceManager\ServiceLocatorFactory;

class Token implements ServiceLocatorAwareInterface
{
    use \Zend\ServiceManager\ServiceLocatorAwareTrait;
    
    const TYPE_BEARER = "Bearer";
    
    /**
     * @var string
     */
    protected $_accessToken;
    
    /**
     * @var string
     */
    protected $_refreshToken;
    
    /**
     * @var \DateTime
     */
    protected $_expiresAt;
    
    /**
     * @var unknown
     */
    protected $_tokenType;
    
    /**
     * @var \GoogleGlass\Entity\OAuth2\Jwt;
     */
    protected $_jwt;
    
    public function __sleep()
    {
        return array(
            '_accessToken', '_refreshToken', '_expiresAt', '_tokenType', '_jwt'
        );
    }
    
    public function __wakeup()
    {
        $this->setServiceLocator(ServiceLocatorFactory::getInstance());
    }
    
    /**
     * @return \GoogleGlass\Entity\OAuth2\Jwt
     */
    public function getJwt() {
        return $this->_jwt;
    }

    /**
     * @param \GoogleGlass\Entity\OAuth2\JWT $_jwt
     * @return self
     */
    public function setJwt(\GoogleGlass\Entity\OAuth2\Jwt $_jwt) {
        $this->_jwt = $_jwt;
        return $this;
    }

    /**
     * @param array $result
     * @return \GoogleGlass\Entity\OAuth2\Token
     */
    public function fromApiResult(array $result)
    {
        $this->setAccessToken(isset($result['access_token']) ? $result['access_token'] : null)
               ->setRefreshToken(isset($result['refresh_token']) ? $result['refresh_token'] : null)
               ->setTokenType(isset($result['token_type']) ? $result['token_type'] : null);
        
        if(isset($result['id_token'])) {
            $jwtDecoder = $this->getServiceLocator()->get('GoogleGlass\OAuth2\JWT');
            $jwtObj = $jwtDecoder->decode($result['id_token']);
            $this->setJwt($jwtObj);
        }
        
        $date = new \DateTime('now');
        $date->add(\DateInterval::createFromDateString("+{$result['expires_in']} seconds"));
        
        $this->setExpiresAt($date);
        
        return $this;
    }
    
    public function isValid()
    {
        $expiresAt = $this->getExpiresAt();
        
        if(!$expiresAt instanceof \DateTime)
        {
            return false;
        }
        
        if(is_null($this->getAccessToken())) {
            return false;
        }
        
        if($expiresAt->getTimestamp() > time()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * @return the $_accessToken
     */
    public function getAccessToken() {
        return $this->_accessToken;
    }

    /**
     * @return the $_refreshToken
     */
    public function getRefreshToken() {
        return $this->_refreshToken;
    }

    /**
     * @return the $_expiresAt
     */
    public function getExpiresAt() {
        return $this->_expiresAt;
    }

    /**
     * @return the $_tokenType
     */
    public function getTokenType() {
        return $this->_tokenType;
    }

    /**
     * @param string $_accessToken
     * @return self
     */
    public function setAccessToken($_accessToken) {
        $this->_accessToken = $_accessToken;
        return $this;
    }

    /**
     * @param string $_refreshToken
     * @return self
     */
    public function setRefreshToken($_refreshToken) {
        $this->_refreshToken = $_refreshToken;
        return $this;
    }

    /**
     * @param DateTime $_expiresAt
     * @return self
     */
    public function setExpiresAt(\DateTime $_expiresAt) {
        $this->_expiresAt = $_expiresAt;
        return $this;
    }

    /**
     * @param \GoogleGlass\Entity\OAuth2\unknown $_tokenType
     * @return self
     */
    public function setTokenType($_tokenType) {
        $this->_tokenType = $_tokenType;
        return $this;
    }
}