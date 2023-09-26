<?php

namespace Rokanthemes\Rewards\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $resultPageFactory;
	protected $customerSession;
	protected $urlInterface;
	
	public function __construct( 
		Context $context,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\UrlInterface $urlInterface,
		PageFactory $resultPageFactory
	)
	{
		$this->resultPageFactory = $resultPageFactory;
		$this->customerSession = $customerSession;
		$this->urlInterface = $urlInterface;
		parent::__construct($context);
	} 

	public function execute()
	{
		if(!$this->customerSession->isLoggedIn()) {
			$this->customerSession->setAfterAuthUrl($this->urlInterface->getCurrentUrl());
			$this->customerSession->authenticate();
		}  
		$resultPage = $this->resultPageFactory->create();

		$resultPage->getConfig()->getTitle()->set(__('Rewards'));

		return $resultPage;
	}
}
