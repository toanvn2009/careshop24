<?php

namespace Careshop\CommunityIdea\Model\ResourceModel\Idea;

use Magento\Framework\DB\Select;
use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;
use Careshop\CommunityIdea\Api\Data\SearchResult\IdeaSearchResultInterface;
use Careshop\CommunityIdea\Model\Idea;
use Zend_Db_Select;

class Collection extends AbstractCollection implements IdeaSearchResultInterface
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'idea_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'community_idea_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'idea_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Idea::class, \Careshop\CommunityIdea\Model\ResourceModel\Idea::class);
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @return Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Zend_Db_Select::GROUP);

        return $countSelect;
    }

    /**
     * @param null $valueField
     * @param string $labelField
     * @param array $additional
     *
     * @return array
     */
    protected function _toOptionArray($valueField = null, $labelField = 'name', $additional = [])
    {
        $valueField = 'idea_id';

        return parent::_toOptionArray($valueField, $labelField, $additional); // TODO: Change the autogenerated stub
    }
}
