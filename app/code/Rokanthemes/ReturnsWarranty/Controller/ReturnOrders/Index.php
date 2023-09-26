<?php

namespace Rokanthemes\ReturnsWarranty\Controller\ReturnOrders;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $resultPageFactory;

	public function __construct(
		Context $context,
		PageFactory $resultPageFactory
	)
	{
		$this->resultPageFactory = $resultPageFactory;

		parent::__construct($context);
	}

	public function execute()
	{
		$resultPage = $this->resultPageFactory->create();

		$resultPage->getConfig()->getTitle()->set(__('Returns orders')); 

		return $resultPage;
	}
}
