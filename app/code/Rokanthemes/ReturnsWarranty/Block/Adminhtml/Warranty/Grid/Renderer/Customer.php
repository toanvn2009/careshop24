<?php
 
namespace Rokanthemes\ReturnsWarranty\Block\Adminhtml\Warranty\Grid\Renderer;

use Magento\Store\Model\StoreManagerInterface;


class Customer extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $_storeManager;
	protected $customerData;  
	protected $authSession;

    public function __construct(
        \Magento\Backend\Block\Context $context,
		\Magento\Customer\Model\Customer $customerData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,    
		\Magento\Backend\Model\Auth\Session $authSession,	
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
		$this->customerData = $customerData;
		$this->authSession = $authSession;        
    }


    public function render(\Magento\Framework\DataObject $row)
    {
		$customerData = $this->customerData->load($this->_getValue($row));
		$url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB).'customer/index/edit/id/'.$this->_getValue($row);
		return  '<a href="'.$url.'">'.$customerData->getEmail().'</a>';  
    }
}