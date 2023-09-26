<?php

namespace Rokanthemes\Notifications\Observer;

use Magento\Framework\Event\ObserverInterface;
use Rokanthemes\Notifications\Model\NotificationsFactory;
use Magento\Customer\Api\AccountManagementInterface;

class AccountRegisterObserver implements ObserverInterface
{
	
	protected $_accountFactory;

	/**
	 * @var \Magento\Captcha\Helper\Data
	 */
	protected $_helper;

	/**
	 * @var \Magento\Framework\Message\ManagerInterface
	 */
	protected $messageManager;

	public function __construct(
		\Magento\Framework\Message\ManagerInterface $messageManager,
		NotificationsFactory $accountFactory
	)
	{
		$this->_accountFactory = $accountFactory; 
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
		$customer   = $observer->getEvent()->getCustomer();
		$this->creditAccount = $this->_accountFactory->create();
        $this->creditAccount->loadByCustomerId($customer->getId());
	}
}
