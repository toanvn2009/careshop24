<?php
 
namespace Careshop\Community\ViewModel;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Registry;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use \Magento\Framework\ObjectManagerInterface as ObjectManager;
use Magento\Framework\View\LayoutInterface;

class Community implements \Magento\Framework\View\Element\Block\ArgumentInterface
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
        \Magento\CatalogInventory\Api\StockStateInterface $_stockState,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Catalog\Helper\Image $imageHelper,
        Registry $registry,
        ObjectManager $objectManager,
        LayoutInterface $layout
    ) {
        $this->stockState = $_stockState;
        $this->storeManager = $storeManager;
        $this->getSalableQuantityDataBySku = $getSalableQuantityDataBySku;
        $this->request = $request;
        $this->objectManager = $objectmanager;
        $this->categoryRepository = $categoryRepository;
        $this->priceCurrency = $priceCurrency;
        $this->registry = $registry;
        $this->imageHelper = $imageHelper;
        $this->_objectManager = $objectManager;
        $this->layout = $layout;
        $this->_resource = $resource;
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
     * return sub category html
     */
    public function getLanguagesTranslateFrom($step)
    {
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        $connection = $this->_resource->getConnection();
        $tableName = $this->_resource->getTableName('community_languages_translate');
        $sql = "SELECT * FROM $tableName";
        $languages = $connection->fetchAll($sql);
        $lang_name_default = 'English';
        $lang_code_default = 'en';
        if ($step == 'from') {
            $text = __('Translate from');
        } else {
            $sql = "SELECT * FROM $tableName WHERE code='".$lang."'";
            $result = $connection->fetchRow($sql);
            if ($result && isset($result['name']) && isset($result['code'])) {
                $lang_name_default = $result['name'];
                $lang_code_default = $result['code'];
            }
            $text = __('Translate to');
        }
        $html = '';
        $html .= '<div class="languages_translate languages_translate_'.$step.'">';
            $html .= '<div class="languages-'.$step.'">';
                $html .= $text;
                $html .= '<span class="languages-value">'.$lang_name_default.'</span>';
            $html .= '</div>';
            $html .= '<ul class="languages_list">';
                foreach ($languages as $language) {
                    $html .= '<li class="languages_item" data-value="'.$language['code'].'">'.$language['name'].'</li>';
                }
            $html .= '</ul>';
            $html .= '<input type="hidden" name="language_code" value="'.$lang_code_default.'">';
        $html .= '</div>';
        return $html;
    }

}
