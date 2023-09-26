<?php

namespace Rokanthemes\Notifications\Block;

use Magento\Framework\ObjectManagerInterface;
use Rokanthemes\Notifications\Model\Notifications;
use Magento\Store\Model\StoreManagerInterface;

class Dashboard extends \Magento\Framework\View\Element\Template
{

	protected $objectManager;
	protected $_storeManager;
	protected $_helperView;
	protected $customerSession;
	protected $pricingHelper;
	protected $transactionFactory;
	protected $jsonHelper;
	protected $registry; 

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Customer\Model\Session $customerSession,
		StoreManagerInterface $storeManager,
		ObjectManagerInterface $objectManager,
		Notifications $notifications,
		array $data = []
	)
	{
		$this->objectManager    = $objectManager;
		$this->customerSession  = $customerSession;
		$this->notifications     = $notifications;
		$this->_storeManager = $storeManager;
		parent::__construct($context, $data);
	}

	/**
	 * Returns the Magento Customer Model for this block
	 *
	 * @return \Magento\Customer\Api\Data\CustomerInterface|null
	 */
	public function getChecked($data)  
	{
		if($data == 1){
			return 'checked';
		}
		return false;
	}
	public function getDataNotificationsCustomer()   
	{
		$customerId = $this->customerSession->getId();
		$data = $this->notifications->load($customerId,'customer_id');
		return $data;
	}
	public function getBaseUrl()  
	{
		$url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB).'notifications/changner/save';
		return $url;
	}
}
