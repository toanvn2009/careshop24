<?php

namespace Rokanthemes\Reviews\Block\Customer;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\ObjectManagerInterface;
use Magento\Directory\Helper\Data as DirectoryHelper;

/**
 * Class AbstractAccount
 * @package Magento\Customer\Controller
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Improve extends \Magento\Framework\View\Element\Template
{

	protected $objectManager;
	protected $customerSession;
	protected $_resource;
	protected $_order;
	protected $registry; 
	protected $_productRepositoryFactory;
	protected $request;
	protected $productImage;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepositoryFactory,
		Registry $registry,
		\Magento\Sales\Model\Order $order,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Catalog\Helper\Image $productImage,
		\Magento\Framework\App\ResourceConnection $_resource,
		ObjectManagerInterface $objectManager,
		array $data = []
	)
	{
		$this->objectManager    = $objectManager;
		$this->productImage = $productImage;
		$this->customerSession  = $customerSession;
		$this->_resource = $_resource;
		$this->_order = $order;
		$this->_productRepositoryFactory = $productRepositoryFactory;
		$this->registry         = $registry;
		$this->request = $request;

		parent::__construct($context, $data);
	}

	/**
	 * Returns the Magento Customer Model for this block
	 *
	 * @return \Magento\Customer\Api\Data\CustomerInterface|null
	 */
	
	public function getProductImage()  
	{
		$id = $this->request->getParam('id');
		$product = $this->_productRepositoryFactory->create()->getById($id);
		$image = $product->getData('image');
		$image_url = $this->productImage->init($product, 'product_thumbnail_image')->setImageFile($product->getFile())->resize(200, 200)->getUrl();
		return $image_url;
	}
	public function getProductName()  
	{
		$id = $this->request->getParam('id');
		$product = $this->_productRepositoryFactory->create()->getById($id);
		$name = $product->getData('name');
		return $name;
	}
	public function getProductInfo()  
	{
		$id = $this->request->getParam('id');
		$product = $this->_productRepositoryFactory->create()->getById($id);
		$colorAttributeId = $product->getResource()->getAttribute('color')->getId();
		$configurableAttrs = $product->getTypeInstance()->getConfigurableAttributes($product);
		if(isset($configurableAttrs[$colorAttributeId])){
			echo count($configurableAttrs[$colorAttributeId]['values']);
			echo "<pre>";print_r($configurableAttrs[$colorAttributeId]['values']);
		}
	}
	public function getPurchase()  
	{
		$customer_id = $this->customerSession->getId();
		$id = $this->request->getParam('id');
		$order_collection = $this->_order->getCollection()->addAttributeToFilter('customer_id', $customer_id);
		foreach ($order_collection as $order){ 
			$orderItems = $order->getAllItems();
			foreach ($orderItems as $items){ 
				$product_type = $items->getData('product_type');
				if($product_type == 'simple'){
					$product_id = $items->getData('product_id');
					if($product_id == $id){
						$date = $items->getData('created_at');
						return $date;
					}
				}
			}
		}
		return false;
	}
	public function getPostId($id)  
	{
		$adapter  = $this->_resource->getConnection();
		$customer_id = $this->customerSession->getId();
		$order_collection = $this->_order->getCollection()->addAttributeToFilter('customer_id', $customer_id);
		foreach ($order_collection as $order){ 
			$orderItems = $order->getAllItems();
			foreach ($orderItems as $items){ 
				$product_id = $items->getData('product_id');
				if($product_id == $id){ 
					$parent_item_id = $items->getData('parent_item_id');
					if($parent_item_id){
						$sql = 'SELECT product_id FROM sales_order_item WHERE item_id="'.$parent_item_id.'"';
						$data_query = $adapter->fetchRow($sql);
						$product_id = $data_query['product_id'];
					}else{
						$product_id = $items->getData('product_id');
					}
					return $product_id;
				}
			}
		}
		return false;
	}
}
