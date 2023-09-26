<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Rokanthemes\ReturnsWarranty\Block;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Directory\Helper\Data as DirectoryHelper;


class Dashboard extends \Magento\Framework\View\Element\Template
{

	protected $objectManager;
	protected $_helperView;
	protected $customerSession;
	protected $pricingHelper;
	protected $campaignFactory;
	protected $accountFactory;
	protected $_currentAccount = null;
	protected $transactionFactory;
	protected $withdrawFactory;
	protected $storeManager;
	protected $paymentHelper;
	protected $jsonHelper;
	protected $registry; 
	protected $_productRepositoryFactory;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Customer\Helper\View $helperView,
		JsonHelper $jsonHelper,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepositoryFactory,
		Registry $registry,
		\Magento\Framework\Pricing\Helper\Data $pricingHelper,
		ObjectManagerInterface $objectManager,
		array $data = []
	)
	{
		$this->pricingHelper    = $pricingHelper;
		$this->objectManager    = $objectManager;
		$this->customerSession  = $customerSession;
		$this->_productRepositoryFactory = $productRepositoryFactory;
		$this->_helperView      = $helperView;
		$this->jsonHelper       = $jsonHelper;
		$this->storeManager = $storeManager;
		$this->registry         = $registry;

		parent::__construct($context, $data);
	}
}
