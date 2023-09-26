<?php

namespace Rokanthemes\ReturnsWarranty\Controller\RegisterWarranty;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Post extends \Magento\Framework\App\Action\Action
{
	protected $resultPageFactory;
	protected $storeManager;
	protected $warranty;

	public function __construct(
		Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		PriceCurrencyInterface $priceCurrency,
		\Rokanthemes\ReturnsWarranty\Model\WarrantyFactory $warranty,
		PageFactory $resultPageFactory
	)
	{
		$this->resultPageFactory = $resultPageFactory;
		$this->storeManager = $storeManager;
		$this->warranty = $warranty;
		$this->priceCurrency = $priceCurrency;
		parent::__construct($context);
	}

	public function execute()
	{
		$id = $this->getRequest()->getParam('id');
		$data_query = $this->warranty->getDataOrder($id);
		if($data_query){
			$data_query_data = $this->warranty->getDataOrderByDataQuery($data_query);
			if(!$data_query_data){
				$Warranty = $this->warranty->create();
				$Warranty->setCustomerId($customer_id);
				$Warranty->setProductId($data_query['product_id']);
				$Warranty->setProductName($data_query['name']);
				$Warranty->setOrderId($data_query['order_id']);
				$Warranty->setOrderItemId($data_query['item_id']);
				$Warranty->setQtyOrdered($data_query['qty_ordered']);
				$Warranty->setPrice($data_query['price']);
				$Warranty->save();
				$success = __("Register Warranty Success.");
				$this->messageManager->addSuccessMessage($success);
				return $this->_redirect('returnswarranty/order/view/order_id/'.$data_query['order_id']);
			}else{
				$success = __("Error: Warranty has been added.");
				$this->messageManager->addErrorMessage($success);
				return $this->_redirect('returnswarranty/order/view/order_id/'.$data_query['order_id']);
			}
		}else{
			return $this->_redirect('/');
		}
	}
}
