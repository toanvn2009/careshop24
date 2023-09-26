<?php

namespace Rokanthemes\Notifications\Controller\Changner;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Save extends \Magento\Framework\App\Action\Action
{
	protected $resultPageFactory;
	protected $customerSession;
	protected $notifications;

	public function __construct(
		Context $context,
		\Magento\Customer\Model\Session $customerSession,
		\Rokanthemes\Notifications\Model\Notifications $notifications,
		PageFactory $resultPageFactory
	)
	{
		$this->resultPageFactory = $resultPageFactory;
		$this->customerSession = $customerSession;
		$this->notifications = $notifications;
		parent::__construct($context);
	}

	public function execute()
	{
		$post = $this->getRequest()->getPostValue();
		$package_shipment = (isset($_POST['package_shipment'])) ? 1 : 0;
		$order_confirmation = (isset($_POST['order_confirmation'])) ? 1 : 0;
		$status_update = (isset($_POST['status_update'])) ? 1 : 0;
		$promotions = (isset($_POST['promotions'])) ? 1 : 0;
		$reviews_impovement = (isset($_POST['reviews_impovement'])) ? 1 : 0;
		$subcription_to_newsletter = (isset($_POST['subcription_to_newsletter'])) ? 1 : 0;
		if($this->customerSession->isLoggedIn()) {
			$customerId = $this->customerSession->getId();
			$data = $this->notifications->load($customerId,'customer_id');
			$data->setPackageShipment($package_shipment);
			$data->setOrderConfirmation($order_confirmation);
			$data->setStatusUpdate($status_update);
			$data->setPromotions($promotions);
			$data->setReviewsImpovement($reviews_impovement);
			$data->setSubcriptionToNewsletter($subcription_to_newsletter);
			$data->save();
		}  
		$resultRedirect = $this->resultRedirectFactory->create();
		$resultRedirect->setPath('notifications');
		return $resultRedirect;
	}
}
