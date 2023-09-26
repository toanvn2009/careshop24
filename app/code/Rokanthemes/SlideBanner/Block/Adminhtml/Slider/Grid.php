<?php
 
namespace Rokanthemes\SlideBanner\Block\Adminhtml\Slider;
 
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
		\Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
		$this->_objectManager = $objectManager;
        parent::__construct($context, $backendHelper, $data);
    }
 
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sliderGrid');
        $this->setDefaultSort('slider_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('slider_record');
    }
 
    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
		$collection = $this->_objectManager->create('Rokanthemes\SlideBanner\Model\Slider', [])->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }
 
    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'slider_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'slider_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
		$this->addColumn(
            'slider_identifier',
            [
                'header' => __('Identifier'),
                'type' => 'text',
                'index' => 'slider_identifier',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
		$this->addColumn(
            'slider_title',
            [
                'header' => __('Title'),
                'type' => 'text',
                'index' => 'slider_title',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
		$this->addColumn(
            'slider_status',
            [
                'header' => __('Status'),
                'type' => 'options',
                'index' => 'slider_status',
				'options'=> [1=>__('Enable'), 2=>__('Disable')],
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
		$this->addColumn(
			'edit',
			[
				'header' => __('Action'),
				'type' => 'action',
				'getter' => 'getId',
				'actions' => [
					[
						'caption' => __('Edit'),
						'url' => ['base' => '*/*/edit'],
						'field' => 'slider_id',
						'style' => 'background-color: green;padding: 5px 20px;color: #fff;font-weight: bold;width: 100%;display: block;text-align: center;text-transform: uppercase;border-radius: 5px;'
					]
				], 
				'filter' => false,
				'sortable' => false,
				'header_css_class' => 'col-action',
				'column_css_class' => 'col-action',
			]
		);
		
		$this->addColumn(
			'delete',
			[
				'header' => __('Action'),
				'type' => 'action',
				'getter' => 'getId',
				'actions' => [
					[
						'caption' => __('Delete'),
						'url' => ['base' => '*/*/delete'],
						'field' => 'slider_id',
						'onclick' => "return confirm('Are you sure you want to delete this item?');",
						'style' => 'background-color: red;padding: 5px 20px;color: #fff;font-weight: bold;width: 100%;display: block;text-align: center;text-transform: uppercase;border-radius: 5px;'
					],
				], 
				'filter' => false,
				'sortable' => false,
				'header_css_class' => 'col-action',
				'column_css_class' => 'col-action',
			]
		);
        return parent::_prepareColumns();
    }
}