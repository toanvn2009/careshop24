<?php

namespace Careshop\CommunityIdea\Model\ResourceModel\Topic;

use Magento\Framework\DB\Select;
use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;
use Careshop\CommunityIdea\Api\Data\SearchResult\TopicSearchResultInterface;
use Careshop\CommunityIdea\Model\Topic;
use Careshop\CommunityIdea\Model\ResourceModel\Topic as TopicResourceModel;
use Zend_Db_Select;

class Collection extends AbstractCollection implements TopicSearchResultInterface
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'topic_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'community_topic_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'topic_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Topic::class, TopicResourceModel::class);
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
        $valueField = 'topic_id';

        return parent::_toOptionArray($valueField, $labelField, $additional); // TODO: Change the autogenerated stub
    }

    /**
     * @param mixed $topicIds
     *
     * @return $this
     */
    public function addIdFilter($topicIds)
    {
        $condition = '';

        if (is_array($topicIds)) {
            if (!empty($topicIds)) {
                $condition = ['in' => $topicIds];
            }
        } elseif (is_numeric($topicIds)) {
            $condition = $topicIds;
        } elseif (is_string($topicIds)) {
            $ids = explode(',', $topicIds);
            if (empty($ids)) {
                $condition = $topicIds;
            } else {
                $condition = ['in' => $ids];
            }
        }

        if ($condition) {
            $this->addFieldToFilter('topic_id', $condition);
        }

        return $this;
    }
}