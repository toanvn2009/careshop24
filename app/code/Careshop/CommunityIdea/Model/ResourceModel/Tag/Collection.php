<?php

namespace Careshop\CommunityIdea\Model\ResourceModel\Tag;

use Magento\Framework\DB\Select;
use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;
use Careshop\CommunityIdea\Api\Data\SearchResult\TagSearchResultInterface;
use Careshop\CommunityIdea\Model\Tag;
use Careshop\CommunityIdea\Model\ResourceModel\Tag as TagResourceModel;
use Zend_Db_Select;

class Collection extends AbstractCollection implements TagSearchResultInterface
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'tag_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'community_tag_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'tag_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Tag::class, TagResourceModel::class);
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
        $valueField = 'tag_id';

        return parent::_toOptionArray($valueField, $labelField, $additional); // TODO: Change the autogenerated stub
    }

    /**
     * add if filter
     *
     * @param array|int|string $tagIds
     *
     * @return $this
     */
    public function addIdFilter($tagIds)
    {
        $condition = '';

        if (is_array($tagIds)) {
            if (!empty($tagIds)) {
                $condition = ['in' => $tagIds];
            }
        } elseif (is_numeric($tagIds)) {
            $condition = $tagIds;
        } elseif (is_string($tagIds)) {
            $ids = explode(',', $tagIds);
            if (empty($ids)) {
                $condition = $tagIds;
            } else {
                $condition = ['in' => $ids];
            }
        }

        if ($condition) {
            $this->addFieldToFilter('tag_id', $condition);
        }

        return $this;
    }
}