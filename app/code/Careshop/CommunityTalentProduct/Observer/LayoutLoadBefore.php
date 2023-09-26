<?php
namespace Careshop\CommunityTalentProduct\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Registry;

class LayoutLoadBefore implements ObserverInterface
{

    protected $_registry;
	protected $_scopeConfig;
	protected $_pageResult;
	protected $request;
    protected $helper;

    public function __construct(
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeInterface,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Framework\View\Page\Config $pageConfig,
        Registry $registry
    ){
        $this->_registry = $registry;
		$this->request = $request;
		$this->_pageConfig = $pageConfig;
		$this->_scopeConfig = $scopeInterface;
    }

    public function execute(Observer $observer)
    {
        $action = $observer->getData('full_action_name');
		$params = $this->request->getParams();
        if(isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'categorytab/category/view') !== false || strpos($_SERVER['REQUEST_URI'], 'blueskytechco_quickview/product/view') !== false){
            return $this;
        }
        $layout = $observer->getData('layout');
		
		/* Category page */
        $category = $this->_registry->registry('current_category');
        if($action == 'catalog_category_view' && $category){
            if ($category->getId() == 16) {
                $layout->getUpdate()->addHandle('catalog_category_view_talentproduct');  
                $this->_pageConfig->addBodyClass('community-talent-category');
                return $this;
            }
        }
		
		/* Product page */
		$product = $this->_registry->registry('product');
		if($action == 'catalog_product_view' && $product){
            $developproduct = false;
            $categories = $product->getCategoryIds();
            foreach($categories as $category){
                if ($category == 16) {
                    $layout->getUpdate()->addHandle('catalog_product_view_talentproduct');  
                    $this->_pageConfig->addBodyClass('community-talent-product');
                    break;
                }
            }
            return $this;
		}
    }

}
?>