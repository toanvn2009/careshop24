<?php
 
namespace Rokanthemes\SlideBanner\Block\Adminhtml\Slide;
 
use Magento\Backend\Block\Widget\Grid as WidgetGrid;
 
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;
 
    /**
     * @var \Webkul\Grid\Model\GridFactory
     */
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
        $this->setId('gridGrid');
        $this->setDefaultSort('slide_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('grid_record');
    }
 
    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_objectManager->create('Rokanthemes\SlideBanner\Model\Slide', [])->getCollection()->joinSlider();
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
            'slide_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'slide_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'slider_id',
            [
                'header' => __('Slider'),
                'type' => 'options',
                'index' => 'slider_id',
                'filter_index' => 'main_table.slider_id',
                'options'=> $this->_getSliderOptions()
            ]
        );
        $this->addColumn(
            'slide_status',
            [
                'header' => __('Status'),
                'type' => 'options',
                'index' => 'slide_status',
                'options'=> [1=>__('Enable'), 2=>__('Disable')]
            ]
        );
        $this->addColumn(
            'slide_image',
            [
                'header' => __('Images'),
                'renderer' => 'Rokanthemes\SlideBanner\Block\Adminhtml\Slide\Renderer\Image',
                'filter' => false,
                'order' => false
            ]
        );
        $this->addColumn(
            'slide_image_mobile',
            [
                'header' => __('Images Mobile'),
                'renderer' => 'Rokanthemes\SlideBanner\Block\Adminhtml\Slide\Renderer\Imagemobile',
                'filter' => false,
                'order' => false
            ]
        );
        $this->addColumn(
            'slide_position',
            [
                'header' => __('Position'),
                'type' => 'number',
                'index' => 'slide_position'
            ]
        );

        $this->addColumn(
            'store_ids',
            [
                'header' => __('Store Views'),
                'index' => 'store_ids',                        
                'type' => 'store',
                'renderer'=>  'Rokanthemes\SlideBanner\Block\Adminhtml\Slide\Edit\Tab\Renderer\Store',
                'filter' => false,
                'sortable' => false,
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
                        'field' => 'slide_id',
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
                        'field' => 'slide_id',
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
    protected function _getSliderOptions()
    {
        $result = [];
        $collection = $this->_objectManager->create('Rokanthemes\SlideBanner\Model\Slider', [])->getCollection();
        foreach ($collection as $slider) {
            $result[$slider->getId()] = $slider->getSliderTitle();
        }
        return $result;
    }
}
