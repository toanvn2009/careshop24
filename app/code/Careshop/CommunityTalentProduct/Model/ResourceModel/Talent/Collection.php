<?php

namespace Careshop\CommunityTalentProduct\Model\ResourceModel\Talent;

use Magento\Framework\DB\Select;
use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;
use Careshop\CommunityTalentProduct\Model\Talent;
use Zend_Db_Select;

class Collection extends AbstractCollection
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'talent_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'community_talent_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'talent_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Talent::class, \Careshop\CommunityTalentProduct\Model\ResourceModel\Talent::class);
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
        $valueField = 'talent_id';
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }
}
