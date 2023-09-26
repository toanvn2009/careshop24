<?php

namespace Careshop\CommunityIdea\Block\Adminhtml\Category\Edit\Tab;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Helper\Data;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Careshop\CommunityIdea\Model\IdeaFactory;
use Careshop\CommunityIdea\Model\ResourceModel\Idea\Collection;
use Careshop\CommunityIdea\Model\ResourceModel\Idea\CollectionFactory;

class Idea extends Extended implements TabInterface
{
    /**
     * @var CollectionFactory
     */
    public $ideaCollectionFactory;

    /**
     * @var Registry
     */
    public $coreRegistry;

    /**
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
        $this->ideaCollectionFactory = $ideaCollectionFactory;
        $this->coreRegistry          = $coreRegistry;
        $this->ideaFactory           = $ideaFactory;

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

        if ($this->getCategory() && $this->getCategory()->getId()) {
            $this->setDefaultFilter(['in_ideas' => 1]);
        }
    }

    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        /** @var Collection $collection */
        $collection = $this->ideaCollectionFactory->create();
        $collection->getSelect()->joinLeft(
            ['related' => $collection->getTable('community_idea_category')],
            'related.idea_id=main_table.idea_id AND related.category_id=' . (int) $this->getRequest()->getParam(
                'id',
                0
            ),
            ['position']
        );

        $collection->addFilterToMap('idea_id', 'main_table.idea_id');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_ideas', [
            'header_css_class' => 'a-center',
            'type'             => 'checkbox',
            'name'             => 'in_idea',
            'values'           => $this->_getSelectedIdeas(),
            'align'            => 'center',
            'index'            => 'idea_id'
        ]);
        $this->addColumn('idea_id', [
            'header'           => __('ID'),
            'sortable'         => true,
            'index'            => 'idea_id',
            'type'             => 'number',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
        ]);
        $this->addColumn('title', [
            'header'           => __('Name'),
            'index'            => 'name',
            'header_css_class' => 'col-name',
            'column_css_class' => 'col-name'
        ]);
        $this->addColumn('position', [
            'header'         => __('Position'),
            'name'           => 'position',
            'width'          => 60,
            'type'           => 'number',
            'validate_class' => 'validate-number',
            'index'          => 'position',
            'editable'       => true,
        ]);

        return $this;
    }

    /**
     * Retrieve selected Ideas
     * @return array
     */
    protected function _getSelectedIdeas()
    {
        $ideas = $this->getRequest()->getPost('category_ideas', null);
        if ($this->getCategory() && !is_array($ideas)) {
            $ideas = $this->getCategory()->getIdeasPosition();

            return array_keys($ideas);
        }

        return $ideas;
    }

    /**
     * Retrieve selected Ideas
     * @return array
     */
    public function getSelectedIdeas()
    {
        $selected = $this->getCategory()->getIdeasPosition();
        if (!is_array($selected)) {
            $selected = [];
        } else {
            foreach ($selected as $key => $value) {
                $selected[$key] = ['position' => $value];
            }
        }

        return $selected;
    }

    /**
     * @param \Careshop\CommunityIdea\Model\Idea|Object $item
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
        if($this->getCategory()) 
            return $this->getUrl('*/*/ideasGrid', ['id' => $this->getCategory()->getId()]);
    }

    /**
     * @return \Careshop\CommunityIdea\Model\Category
     */
    public function getCategory()
    {
        return $this->coreRegistry->registry('category');
    }

    /**
     * @param Column $column
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() === 'in_ideas') {
            $ideaIds = $this->_getSelectedIdeas();
            if (empty($ideaIds)) {
                $ideaIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('main_table.idea_id', ['in' => $ideaIds]);
            } elseif ($ideaIds) {
                $this->getCollection()->addFieldToFilter('main_table.idea_id', ['nin' => $ideaIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
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
        return $this->getUrl('community/category/ideas', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }
}
