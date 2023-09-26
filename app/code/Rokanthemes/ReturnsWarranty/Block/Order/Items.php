<?php

namespace Rokanthemes\ReturnsWarranty\Block\Order;

/**
 * Sales order view items block.
 *
 * @api
 * @since 100.0.2
 */
class Items extends \Magento\Sales\Block\Items\AbstractItems
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
	protected $string;
	protected $objectManager;
	protected $productImage;
	protected $warranty;
	protected $_storeManager;
    /**
     * Order items per page.
     *
     * @var int
     */
    private $itemsPerPage;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory
     */
    private $itemCollectionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Item\Collection|null
     */
    private $itemCollection;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory|null $itemCollectionFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
		\Rokanthemes\ReturnsWarranty\Model\Warranty $warranty,
		\Magento\Store\Model\StoreManagerInterface $storeManager,    
		\Magento\Catalog\Model\Product $product,
		\Magento\Catalog\Helper\Image $productImage,
        array $data = [],
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemCollectionFactory = null
    ) {
        $this->_coreRegistry = $registry;
		$this->product = $product;
		$this->warranty = $warranty;
		$this->productImage = $productImage;
		$this->_storeManager = $storeManager;
        $this->itemCollectionFactory = $itemCollectionFactory ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory::class);
        parent::__construct($context, $data);
    }

    /**
     * Init pager block and item collection with page size and current page number
     *
     * @return $this
     * @since 100.1.7
     */
    protected function _prepareLayout()
    {
        $this->itemsPerPage = $this->_scopeConfig->getValue('sales/orders/items_per_page');

        $this->itemCollection = $this->itemCollectionFactory->create();
        $this->itemCollection->setOrderFilter($this->getOrder());

        /** @var \Magento\Theme\Block\Html\Pager $pagerBlock */
        $pagerBlock = $this->getChildBlock('sales_order_item_pager');
        if ($pagerBlock) {
            $pagerBlock->setLimit($this->itemsPerPage);
            //here pager updates collection parameters
            $pagerBlock->setCollection($this->itemCollection);
            $pagerBlock->setAvailableLimit([$this->itemsPerPage]);
            $pagerBlock->setShowAmounts($this->isPagerDisplayed());
        }

        return parent::_prepareLayout();
    }

    /**
     * Determine if the pager should be displayed for order items list.
     *
     * To be called from templates(after _prepareLayout()).
     *
     * @return bool
     * @since 100.1.7
     */
    public function isPagerDisplayed()
    {
        $pagerBlock = $this->getChildBlock('sales_order_item_pager');
        return $pagerBlock && ($this->itemCollection->getSize() > $this->itemsPerPage);
    }

    /**
     * Get visible items for current page.
     *
     * To be called from templates(after _prepareLayout()).
     *
     * @return \Magento\Framework\DataObject[]
     * @since 100.1.7
     */
    public function getItems()
    {
        return $this->itemCollection->getItems();
    }
	
	public function getProductImage($id)  
	{
		$product = $this->product->load($id);
		$image = $product->getData('image');
		$image_url = $this->productImage->init($product, 'product_thumbnail_image')->setImageFile($product->getFile())->resize(200, 200)->getUrl();
		return $image_url;
	}
	
	public function getAction($id)
    {
		$data_query = $this->warranty->getDataWarrantyByOrderIdItem($id);
		if($data_query){
			$html = '<div>'.__('You Did Register Warranty Product').'</div>'; 
		}else{
			$html = '<a href="'.$this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB).'returnswarranty/registerwarranty/post/id/'.$id.'" >'.__('Register Warranty Product').'</a>';
		}
		return $html;
    }
	
    /**
     * Get pager HTML according to our requirements.
     *
     * To be called from templates(after _prepareLayout()).
     *
     * @return string HTML output
     * @since 100.1.7
     */
    public function getPagerHtml()
    {
        /** @var \Magento\Theme\Block\Html\Pager $pagerBlock */
        $pagerBlock = $this->getChildBlock('sales_order_item_pager');
        return $pagerBlock ? $pagerBlock->toHtml() : '';
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }
}
