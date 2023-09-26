<?php

namespace Careshop\CommunityTesterProduct\Model\ResourceModel\Tester;

use Magento\Framework\DB\Select;
use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;
use Careshop\CommunityTesterProduct\Model\Tester;
use Zend_Db_Select;

class Collection extends AbstractCollection
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'tester_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'community_tester_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'tester_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Tester::class, \Careshop\CommunityTesterProduct\Model\ResourceModel\Tester::class);
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
        $valueField = 'tester_id';
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }
}
