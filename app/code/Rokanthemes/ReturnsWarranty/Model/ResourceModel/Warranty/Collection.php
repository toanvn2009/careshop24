<?php

namespace Rokanthemes\ReturnsWarranty\Model\ResourceModel\Warranty;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
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
    protected $_eventPrefix = 'rokanthemes_returnswarranty_warranty_collection';

    /**
     * Event object
     * 
     * @var string
     */
    protected $_eventObject = 'warranty_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Rokanthemes\ReturnsWarranty\Model\Warranty', 'Rokanthemes\ReturnsWarranty\Model\ResourceModel\Warranty');
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(\Zend_Db_Select::GROUP);
        return $countSelect;
    }
    /**
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     */
}
