<?php
 
namespace Rokanthemes\ReturnsWarranty\Block\Adminhtml\Warranty;

use Magento\Backend\Block\Widget\Grid as WidgetGrid;
 
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;
 
    protected $_collection;
 
    /**
     * @var \Webkul\Grid\Model\Status
     */
    protected $_status;
    protected $_objectManager;
	protected $_warrantyFactory;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
		\Rokanthemes\ReturnsWarranty\Model\WarrantyFactory $warrantyFactory,
		\Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
		$this->_objectManager = $objectManager;
		$this->_warrantyFactory = $warrantyFactory;
        parent::__construct($context, $backendHelper, $data);
    }
 
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('warrantyGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(false);
    }

    protected function _prepareCollection()
    {
		$collection = $this->_warrantyFactory->create()->getCollection(); 
		$this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }
 
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'entity_id'
            ]
        );
		$this->addColumn(
            'customer_id',
            [
                'header' => __('Customer Id'),
                'index' => 'customer_id',
				'renderer'  => '\Rokanthemes\ReturnsWarranty\Block\Adminhtml\Warranty\Grid\Renderer\Customer'
            ]
        );
        $this->addColumn(
            'order_id',
            [
                'header' => __('Order Id'),
                'index' => 'order_id',
				'renderer'  => '\Rokanthemes\ReturnsWarranty\Block\Adminhtml\Warranty\Grid\Renderer\Order'
            ]
        );
		$this->addColumn(
            'product_name', 
            [
                'header' => __('Product Name'),
                'index' => 'product_name' 
            ]
        );
        $this->addColumn(
            'qty_ordered',
            [
                'header' => __('Qty'),
                'index' => 'qty_ordered'
            ]
        );
		$this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status'
            ]
        );
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'index' => 'price'
            ]
        );
        return parent::_prepareColumns();
    }
}