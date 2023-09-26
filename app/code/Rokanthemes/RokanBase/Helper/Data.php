<?php

namespace Rokanthemes\RokanBase\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    protected $_escaper;
    protected $_productImageHelper;
    protected $_themeconfig;
    protected $_product;
    protected $_storeManager;
    protected $_customerSession;
    protected $_resourceConnection;
    protected $_countries;
    
    public function __construct(
        \Magento\Framework\Escaper $_escaper,
        \Magento\Catalog\Helper\Image $productImageHelper,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->_escaper = $_escaper;
        $this->_productImageHelper = $productImageHelper;
        $this->_productloader = $_productloader;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->_resourceConnection = $resourceConnection;
        $this->_countries = $countryFactory->create();
        parent::__construct($context);
    }

    public function getAllowedCountries()
    {
        return $this->_countries->getResourceCollection()->loadByStore()->toOptionArray();
    }
    
    public function getConfigData($path)
    {
        $value = $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $value;
    }
    
    public function getInstockLabel($product)
    {
        if ($label = $product->getDeliveryLabel()) {
            return $label;
        }
        if ($label = $this->getConfigData('cataloginventory/options/delivery_label')) {
            return $label;
        }
        return __('In Stock');
    }
    
    public function checkEnableModuleQuickview()
    {
        $value = $this->getConfigData('rokanthemes_quickview/general/enabled');
        return $value;
    }
    
    protected function getRatioProductImage($width, $height)
    {
        if ($width && $height) {
            return $height / $width;
        }
        return 1;
    }

    public function getUrlProductImage($product, $imageId, $width, $height = null)
    {
        $resizedImage = $this->_productImageHelper
           ->init($product, $imageId)
           ->constrainOnly(true)
           ->keepAspectRatio(true)
           ->keepTransparency(true)
           ->keepFrame(false)
           ->resize($width, $height);
        
        return $resizedImage->getUrl();
    }
    
    function checkHasAddToCartProduct($product)
    {
        if (!$product->getData('has_options') && $product->getTypeID() == 'simple') {
            return true;
        }
        return false;
    }

    public function getLoadProductById($id)
    {
           return $this->_productloader->create()->load($id);
    }

    public function getUrlMedia()
    {
           $storeManager = $this->_storeManager->getStore();
        return $storeManager->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function getCurrencyCode()
    {
           $storeManager = $this->_storeManager->getStore();
        return $storeManager->getCurrentCurrencyCode();
    }

    public function getStoreCode()
    {
           $storeManager = $this->_storeManager->getStore();
        return $storeManager->getCode();
    }

    public function isLoggedIn()
    {
        if ($this->_customerSession->isLoggedIn()) {
            return true;
        }
        return false;
    }

    public function getCustomerFullName()
    {
        if ($this->_customerSession->isLoggedIn()) {
            return $this->_customerSession->getCustomer()->getName();
        }
        return '';
    }

    public function getSubstrCustomerFullName()
    {
        if ($this->_customerSession->isLoggedIn()) {
            $firstname = substr($this->_customerSession->getCustomer()->getFirstname(), 0, 1);
            $lastname = substr($this->_customerSession->getCustomer()->getLastname(), 0, 1);
            return $firstname.$lastname;
        }
        return '';
    }

    public function getSalesOrderItemByItemId($id)
    {
           $connection  = $this->_resourceConnection->getConnection();
           $sql_data = "SELECT item_id FROM sales_order_item WHERE parent_item_id='".$id."'";
           return $connection->fetchRow($sql_data);
    }
    public function getAmastyRmaRequestItem($id)
    {
           $connection  = $this->_resourceConnection->getConnection();
           $sql_data = "SELECT request_item_id FROM amasty_rma_request_item WHERE order_item_id='".$id."'";
           return $connection->fetchRow($sql_data);
    }
    public function getAmastyRmaRequest($id)
    {
           $connection  = $this->_resourceConnection->getConnection();
           $sql_data = "SELECT created_at FROM amasty_rma_request WHERE order_id='".$id."'";
           return $connection->fetchRow($sql_data);
    }
    public function getCurrentCurrencySymbol()
    {
        return $this->_storeManager->getStore()->getBaseCurrency()->getCurrencySymbol();
    } 
}
