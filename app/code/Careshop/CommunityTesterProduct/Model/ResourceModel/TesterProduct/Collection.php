<?php

namespace Careshop\CommunityTesterProduct\Model\ResourceModel\TesterProduct;

use Magento\Framework\DB\Select;
use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;
use Careshop\CommunityTesterProduct\Model\TesterProduct;
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
    protected $_eventPrefix = 'community_tester_product_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'tester_product_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(TesterProduct::class, \Careshop\CommunityTesterProduct\Model\ResourceModel\TesterProduct::class);
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
