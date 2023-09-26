<?php

namespace Rokanthemes\RokanBase\Block\Account;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Directory\Helper\Data as DirectoryHelper;

/**
 * Class AbstractAccount
 * @package Magento\Customer\Controller
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Order extends \Magento\Framework\View\Element\Template
{

	protected $objectManager;
	protected $customerSession;
	protected $_resource;
	protected $_order;
	protected $_review;
	protected $registry; 
	protected $_productRepositoryFactory;
	protected $request;
	protected $productImage;
	protected $storeManager;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Customer\Block\Account\AuthorizationLink $customerSession,
		\Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepositoryFactory,
		Registry $registry,
		\Magento\Sales\Model\Order $order,
		\Rokanthemes\Reviews\Model\Review $review,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Catalog\Helper\Image $productImage,
		\Magento\Framework\App\ResourceConnection $_resource,
		ObjectManagerInterface $objectManager,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		array $data = []
	)
	{
		$this->objectManager    = $objectManager;
		$this->storeManager = $storeManager;
		$this->productImage = $productImage;
		$this->customerSession  = $customerSession;
		$this->_resource = $_resource;
		$this->_order = $order;
		$this->_review = $review;
		$this->_productRepositoryFactory = $productRepositoryFactory;
		$this->registry         = $registry;
		$this->request = $request;

		parent::__construct($context, $data);
	}
	
	public function customerSession(){
		return $this->customerSession;
	}
}
