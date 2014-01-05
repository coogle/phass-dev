<?php

namespace GoogleGlass\Entity;

class NotificationConfig
{
    /**
     * @var \DateTime
     */
    protected $_deliveryTime;
    
    /**
     * @var string
     */
    protected $_level;
	/**
	 * @return the $_deliveryTime
	 */
	public function getDeliveryTime() {
		return $this->_deliveryTime;
	}

	/**
	 * @return the $_level
	 */
	public function getLevel() {
		return $this->_level;
	}

	/**
	 * @param DateTime $_deliveryTime
	 * @return self
	 */
	public function setDeliveryTime($_deliveryTime) {
		$this->_deliveryTime = $_deliveryTime;
		return $this;
	}

	/**
	 * @param string $_level
	 * @return self
	 */
	public function setLevel($_level) {
		$this->_level = $_level;
		return $this;
	}

}