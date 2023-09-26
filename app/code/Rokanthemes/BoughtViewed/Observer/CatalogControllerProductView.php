<?php

namespace Rokanthemes\BoughtViewed\Observer;

use Magento\Framework\Event\ObserverInterface;
use Rokanthemes\BoughtViewed\Model\Viewed;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

class CatalogControllerProductView implements ObserverInterface
{
	
	protected $_viewed;

	/**
	 * @var \Magento\Framework\Message\ManagerInterface
	 */
	protected $messageManager;
	public $date;

	public function __construct(
		\Magento\Framework\Message\ManagerInterface $messageManager,
		DateTime $date,
		Viewed $viewed
	)
	{
		$this->_viewed = $viewed; 
		$this->date  = $date;
		$this->messageManager  = $messageManager;
	}

	/**
	 * Check Captcha On User Login Page
	 *
	 * @param \Magento\Framework\Event\Observer $observer
	 * @return $this
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$product = $observer->getProduct();
		$product_id = $product->getId();		
		$ip = $this->_viewed->getIpAddress();
		$viewed = $this->_viewed->loadByIpAddress();
		if($viewed){
			$viewed->setProductId($product_id); 
			$viewed->setIpAddress($ip); 
			$viewed->setCreatedAt($this->date->date()); 
			$viewed->save(); 
		}else{
			$viewed = $this->_viewed->create();
			$viewed->setProductId($product_id); 
			$viewed->setIpAddress($ip); 
			$viewed->setCreatedAt($this->date->date()); 
			$viewed->save();
		}
	}
}
