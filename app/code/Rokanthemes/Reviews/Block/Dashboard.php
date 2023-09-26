<?php

namespace Rokanthemes\Reviews\Block;

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
class Dashboard extends \Magento\Framework\View\Element\Template
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
		\Magento\Customer\Model\Session $customerSession,
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
		$this->productImage = $productImage;
		$this->storeManager = $storeManager;
		$this->customerSession  = $customerSession;
		$this->_resource = $_resource;
		$this->_order = $order;
		$this->_review = $review;
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
	public function getReviews()  
	{
		$adapter  = $this->_resource->getConnection();
		$customer_id = $this->customerSession->getId();
		$order_collection = $this->_order->getCollection()->addAttributeToFilter('customer_id', $customer_id);
		$product_ids = [];
		foreach ($order_collection as $order){ 
			$orderItems = $order->getAllItems();
			foreach ($orderItems as $items){ 
				$product_type = $items->getData('product_type');
				if($product_type == 'simple'){
					$id = $items->getData('product_id');
					$data_query = $this->Review($id);
					$data_query_improve = $this->ImproveReview($id);
					if(!$data_query || !$data_query_improve){
						$product_ids[] = $items->getData('product_id');
					}
				}
			}
		}
		$product_ids = array_unique($product_ids);
		return $product_ids;
	}
	
	public function getImage($product_id)  
	{
		$product = $this->_productRepositoryFactory->create()->getById($product_id);
		$image = $product->getData('image');
		$image_url = $this->productImage->init($product, 'product_thumbnail_image')->setImageFile($product->getFile())->resize(150, 150)->getUrl();
		return $image_url;
	}
	public function getName($product_id)  
	{
		$product = $this->_productRepositoryFactory->create()->getById($product_id);
		$name = $product->getData('name');
		return $name;
	}
	public function getTitelHeader($product_id)  
	{
		$data_query = $this->Review($product_id);
		$data_query_improve = $this->ImproveReview($product_id);
		$html = '';
		if(!$data_query && !$data_query_improve){
			$html = ''.__('Read To Review / Improve').'';
		}elseif(!$data_query && $data_query_improve){
			$html = ''.__('Read To Review').'';
		}elseif($data_query && !$data_query_improve){
			$html = ''.__('Read To Improve').'';
		}
		return $html;
	}
	public function getAction($product_id)  
	{
		$data_query = $this->Review($product_id);
		$data_query_improve = $this->ImproveReview($product_id);
		$html = '';
		if(!$data_query && !$data_query_improve){
			$html = '<a href="'.$this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB).'reviews/customer/review/id/'.$product_id.'" class="btn">'.__('Review / Improve').'</a>';
		}elseif(!$data_query && $data_query_improve){
			$html = '<a href="'.$this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB).'reviews/customer/review/id/'.$product_id.'" class="btn">'.__('Improve').'</a>';
		}elseif($data_query && !$data_query_improve){
			$html = '<a href="'.$this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB).'reviews/customer/improve/id/'.$product_id.'" class="btn">'.__('Improve').'</a>';
		}
		return $html;
	}
	
	public function Review($product_id){
		$data = $this->_review->dataReviewsByProductId($product_id);
		return $data;
	}
	public function ImproveReview($product_id){ 
		$data = $this->_review->dataImproveReviewByProductId($product_id);
		return $data;
	}
}
