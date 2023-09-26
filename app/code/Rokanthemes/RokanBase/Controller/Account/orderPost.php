<?php

namespace Rokanthemes\RokanBase\Controller\Account;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class orderPost extends \Magento\Framework\App\Action\Action
{
	protected $resultPageFactory;
	protected $storeManager;
	protected $_data;

	public function __construct(
		Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Rokanthemes\RokanBase\Model\Data $data,
		PriceCurrencyInterface $priceCurrency,
		PageFactory $resultPageFactory
	)
	{
		$this->resultPageFactory = $resultPageFactory;
		$this->_data = $data;
		$this->storeManager = $storeManager;
		$this->priceCurrency = $priceCurrency;
		parent::__construct($context);
	}

	public function execute()
	{
		$orders = strval($this->getRequest()->getParam('orders'));
		$email = strval($this->getRequest()->getParam('email'));
		$html = ''; 
		$data_query = $this->_data->getOrders($orders,$email);
		if($data_query){
			foreach ($data_query as $data){
				$html .= '<div class="order">';
					$html .= '<div class="order-info">';
						$html .= '<div class="id">'.__('Order').' : #'.$data['increment_id'].'</div>';
						$html .= '<div class="date">'.__('Order Date').' : '.date("d M Y", strtotime($data['created_at'])).'</div>'; 
						$html .= '<div class="total">'.__('Price').' : <span class="price">'.$this->priceCurrency->convertAndFormat($data['base_grand_total']).'</span></div>';
						$html .= '<div class="col status">'.__('Status').' : '.$data['status'].'</div>'; 
					$html .= '</div>';
				$html .= '</div>';
			}	
		}else{
			$html = '<p>'.__('No results were found').'</p>';
		}
		
		echo json_encode(array('html' => $html));
		die;

		echo json_encode(array('html' => $html));
		die;
	}
}
