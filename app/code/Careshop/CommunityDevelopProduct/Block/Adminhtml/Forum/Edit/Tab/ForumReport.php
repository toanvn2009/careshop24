<?php

namespace Careshop\CommunityDevelopProduct\Block\Adminhtml\Forum\Edit\Tab;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Helper\Data;
use Magento\Framework\Registry;
use Careshop\CommunityDevelopProduct\Model\ResourceModel\ReportForum\Collection;
use Careshop\CommunityDevelopProduct\Block\Adminhtml\Forum\Edit\Tab\Renderer\ReportForum\Action;
use Careshop\CommunityDevelopProduct\Block\Adminhtml\Forum\Edit\Tab\Renderer\ReportForum\Customer;


class ForumReport extends Extended implements TabInterface 
{

    /**
     * @var Registry
     */
    public $coreRegistry;

    /**
     * @var Collection
     */
    protected $historyCollection;

    /**
     * Product constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Data $backendHelper
     * @param Collection $historyCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Data $backendHelper,
        Collection $commentForumCollection,
        array $data = []
    ) {
        $this->coreRegistry      = $coreRegistry;
        $this->commentForumCollection = $commentForumCollection;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Set grid params
     */
    public function _construct()
    {
        parent::_construct();

        $this->setId('entity_id');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        /** @var Collection $collection */
        $collection = $this->commentForumCollection;
        $id         = $this->getRequest()->getParam('id', 0);
        $collection->addFieldToFilter('forum_id', $id);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', [
            'header'           => __('ID'),
            'sortable'         => true,
            'index'            => 'entity_id',
            'type'             => 'number',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
        ]);
        $this->addColumn(
            'customer_id',
            [
                'header'           => __('Customer Name'),
                'index'            => 'customer_id',
                'sortable'         => true,
                'renderer'         => Customer::class
            ]
        ); 
        $this->addColumn('description', [
            'header'           => __('Description'),
            'index'            => 'description',
            'header_css_class' => 'col-short-description',
            'column_css_class' => 'col-short-description'
        ]);
        
        $this->addColumn('created_at', [
            'header'           => __('Modified At'),
            'index'            => 'created_at',
            'header_css_class' => 'col-created-at',
            'column_css_class' => 'col-created-at'
        ]);
        $this->addColumn(
            'action',
            [
                'header'           => __('Action'),
                'index'            => 'template_id',
                'sortable'         => false,
                'filter'           => false,
                'no_link'          => true,
                'renderer'         => Action::class,
                'header_css_class' => 'col-actions',
                'column_css_class' => 'col-actions'
            ]
        );

        return $this;
    }

    /**
     * @param Tag|Object $item
     *
     * @return string
     */
    public function getRowUrl($item)
    {
        return '#';
    }

    /**
     * get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/reportGrid', ['id' => $this->getDevelopForum()->getId()]);
    }

    /**
     * @return \Careshop\CommunityIdea\Model\Idea
     */
    public function getDevelopForum()
    {
        return $this->coreRegistry->registry('community_develop_forum');
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return __('Edit Develop Forum Report');
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('communitydevelop/forum/report', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }
}
