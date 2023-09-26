<?php

namespace Rokanthemes\ReturnsWarranty\Controller\RegisterWarranty;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Controller\ResultFactory;

class Search extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    protected $storeManager;
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
        $id = strval($this->getRequest()->getParam('val'));
        $data_query = $this->warranty->getDataSearchOrder($id);
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData(['orders' => $data_query]);
        return $resultJson;
    }
}
