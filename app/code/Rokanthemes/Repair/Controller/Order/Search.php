<?php

namespace Rokanthemes\Repair\Controller\Order;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Controller\ResultFactory;

class Search extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    protected $storeManager;
    protected $repair;
    protected $customerSession;

    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Rokanthemes\Repair\Model\Repair $repair,
        \Magento\Customer\Model\Session $customerSession,
        PriceCurrencyInterface $priceCurrency,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->storeManager = $storeManager;
        $this->repair = $repair;
        $this->customerSession = $customerSession;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context);
    }

    /**
     *
     * @return List Orders
     */
    public function execute()
    {
        $id = strval($this->getRequest()->getParam('orders'));
        $lastname = strval($this->getRequest()->getParam('lastname'));
        $customer_id = $this->customerSession->getId();
        $data_query = $this->repair->getDataSearch($id, $lastname, $customer_id);
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData(['orders' => $data_query]);
        return $resultJson;
    }
}
