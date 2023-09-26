<?php

namespace Careshop\CommunityDevelopProduct\Model\ResourceModel\CommentForum;

use Magento\Framework\DB\Select;
use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;
use Careshop\CommunityDevelopProduct\Model\CommentForum;
use Zend_Db_Select;

class Collection extends AbstractCollection 
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'community_develop_comment_forum_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'develop_comment_forum_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CommentForum::class, \Careshop\CommunityDevelopProduct\Model\ResourceModel\CommentForum::class);
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
        $valueField = 'entity_id';
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }
}
