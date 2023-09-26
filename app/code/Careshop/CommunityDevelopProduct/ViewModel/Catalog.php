<?php
 
namespace Careshop\CommunityDevelopProduct\ViewModel;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Registry;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use \Magento\Framework\ObjectManagerInterface as ObjectManager;
use Careshop\CommunityDevelopProduct\Model\DevelopFactory;
use Magento\Framework\View\LayoutInterface;

class Catalog implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectmanager;

    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */
    protected $_stockState;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;
    
    /**
     * @var GetSalableQuantityDataBySku
     */
    protected $getSalableQuantityDataBySku;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        GetSalableQuantityDataBySku $getSalableQuantityDataBySku,
        PriceCurrencyInterface $priceCurrency,
        DevelopFactory $develop,
        \Magento\CatalogInventory\Api\StockStateInterface $_stockState,
        \Magento\Catalog\Helper\Image $imageHelper,
        Registry $registry,
        ObjectManager $objectManager,
        LayoutInterface $layout
    ) {
        $this->stockState = $_stockState;
        $this->storeManager = $storeManager;
        $this->getSalableQuantityDataBySku = $getSalableQuantityDataBySku;
        $this->request = $request;
        $this->develop = $develop;
        $this->objectManager = $objectmanager;
        $this->categoryRepository = $categoryRepository;
        $this->priceCurrency = $priceCurrency;
        $this->registry = $registry;
        $this->imageHelper = $imageHelper;
        $this->_objectManager = $objectManager;
        $this->layout = $layout;
    }
    
    /**
     * return params url
     */
    public function getParams()
    {
        $params = $this->request->getParams();
        return $params;
    }

    /**
     * return product
     */
    public function getProduct()
    {
        $product = $this->registry->registry('product');
        return $product;
    } 

    public function getResourceDevelop()
    {
        $resource = $this->develop->create()->getResource();
        return $resource;
    }

    public function getDevelopProduct($product_id)
    {
        $data = $this->getResourceDevelop()->getDevelop($product_id); 
        return $data;
    }

    public function getDevelopComment($product_id)
    {
        $data = $this->getResourceDevelop()->getDevelopComment($product_id); 
        return $data;
    }

}
