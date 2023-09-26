<?php

namespace Rokanthemes\ReturnsWarranty\Block;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Directory\Helper\Data as DirectoryHelper;


class RegisterWarranty extends \Magento\Framework\View\Element\Template
{

	protected $objectManager;
	protected $_helperView;
	protected $customerSession;
	protected $pricingHelper;
	protected $productImage;
	protected $warranty;
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
	protected $orderData;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Customer\Helper\View $helperView,
		JsonHelper $jsonHelper,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepositoryFactory,
		Registry $registry,
		\Magento\Sales\Model\Order $orderData, 
		\Rokanthemes\ReturnsWarranty\Model\Warranty $warranty,
		\Magento\Catalog\Helper\Image $productImage,
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
		$this->warranty = $warranty;
		$this->productImage = $productImage;
		$this->orderData = $orderData;
		parent::__construct($context, $data);
	}
	
	
	public function getWarrantys(){
		$data_query = $this->warranty->getDataWarrantys();
		return $data_query;
	}
	public function getOrder($id){
		$order = $this->orderData->load($id);
		return $order;
	}
	public function getProductImage($id)  
	{
		$product = $this->_productRepositoryFactory->create()->getById($id);
		$image = $product->getData('image');
		$image_url = $this->productImage->init($product, 'product_thumbnail_image')->setImageFile($product->getFile())->resize(200, 200)->getUrl();
		return $image_url;
	}
}
