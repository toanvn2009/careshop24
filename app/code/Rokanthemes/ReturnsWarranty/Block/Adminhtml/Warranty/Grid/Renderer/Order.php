<?php
 
namespace Rokanthemes\ReturnsWarranty\Block\Adminhtml\Warranty\Grid\Renderer;

use Magento\Store\Model\StoreManagerInterface;


class Order extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $_storeManager;
	protected $orderData;
	protected $authSession;

    public function __construct(
        \Magento\Backend\Block\Context $context,
		\Magento\Sales\Model\Order $orderData, 
        \Magento\Store\Model\StoreManagerInterface $storeManager,    
		\Magento\Customer\Model\Customer $customerData,
		\Magento\Backend\Model\Auth\Session $authSession,	
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
		$this->authSession = $authSession;      
		$this->orderData = $orderData;
    }


    public function render(\Magento\Framework\DataObject $row)
    { 
		
		$order = $this->orderData->load($this->_getValue($row));
		$url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB).'sales/order/view/order_id/'.$this->_getValue($row);
		return  '<a href="'.$url.'">#'.$order->getIncrementId().'</a>';  
    }
}