<?php

namespace Rokanthemes\ReturnsWarranty\Controller\ReturnOrders;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Controller\ResultFactory;

class Search extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    protected $storeManager;
    protected $priceCurrency;
    protected $warranty;

    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency,
        \Rokanthemes\ReturnsWarranty\Model\Warranty $warranty,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
        $this->warranty = $warranty;
        parent::__construct($context);
    }

    /**
     *
     * @return List Orders
     */
    public function execute()
    {
        $orders = strval($this->getRequest()->getParam('orders'));
        $startdate = strval($this->getRequest()->getParam('startdate'));
        $endorders = strval($this->getRequest()->getParam('endorders'));
        $data_query = $this->warranty->getDataSearchOrderByDate($orders, $startdate, $endorders);
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData(['orders' => $data_query]);
        return $resultJson;
    }
}
