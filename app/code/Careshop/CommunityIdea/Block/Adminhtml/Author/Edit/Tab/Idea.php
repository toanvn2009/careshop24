<?php

namespace Careshop\CommunityIdea\Block\Adminhtml\Author\Edit\Tab;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Helper\Data;
use Magento\Framework\Registry;
use Careshop\CommunityIdea\Model\IdeaFactory;
use Careshop\CommunityIdea\Model\ResourceModel\Idea\Collection;
use Careshop\CommunityIdea\Model\ResourceModel\Idea\CollectionFactory;

class Idea extends Extended implements TabInterface
{
    /**
     * Idea collection factory
     *
     * @var CollectionFactory
     */
    public $ideaCollectionFactory;

    /**
     * Registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * Idea factory
     *
     * @var IdeaFactory
     */
    public $ideaFactory;

    /**
     * Idea constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Data $backendHelper
     * @param IdeaFactory $ideaFactory
     * @param CollectionFactory $ideaCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Data $backendHelper,
        IdeaFactory $ideaFactory,
        CollectionFactory $ideaCollectionFactory,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->ideaFactory = $ideaFactory;
        $this->ideaCollectionFactory = $ideaCollectionFactory;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Set grid params
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('idea_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        /** @var Collection $collection */
        $collection = $this->ideaCollectionFactory->create();
        $collection->addFieldToFilter('author_id', $this->getAuthor()->getId());

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('idea_id', [
            'header' => __('ID'),
            'sortable' => true,
            'index' => 'idea_id',
            'type' => 'number',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
        ]);
        $this->addColumn('title', [
            'header' => __('Name'),
            'index' => 'name',
            'header_css_class' => 'col-name',
            'column_css_class' => 'col-name'
        ]);
        $this->addColumn('publish_date', [
            'header' => __('Published'),
            'index' => 'publish_date',
            'header_css_class' => 'col-name',
            'column_css_class' => 'col-name'
        ]);

        return $this;
    }

    /**
     * @param \Careshop\CommunityIdea\Model\Idea|Object $item
     *
     * @return string
     */
    public function getRowUrl($item)
    {
        return $this->getUrl('community/idea/edit', ['id' => $item->getId()]);
    }

    /**
     * get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/ideasgrid', ['id' => $this->getAuthor()->getId()]);
    }

    /**
     * @return \Careshop\CommunityIdea\Model\Author
     */
    public function getAuthor()
    {
        return $this->coreRegistry->registry('community_author');
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return __('Ideas');
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
        return $this->getUrl('community/author/ideas', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }
}
