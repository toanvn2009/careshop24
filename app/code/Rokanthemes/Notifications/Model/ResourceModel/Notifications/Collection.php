<?php

namespace Rokanthemes\Notifications\Model\ResourceModel\Notifications;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * ID Field Name
     * 
     * @var string
     */
    protected $_idFieldName = 'notifications_id';

    /**
     * Event prefix
     * 
     * @var string
     */
    protected $_eventPrefix = 'rokanthemes_notifications_notifications_collection';

    /**
     * Event object
     * 
     * @var string
     */
    protected $_eventObject = 'account_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Rokanthemes\Notifications\Model\Notifications', 'Rokanthemes\Notifications\Model\ResourceModel\Notifications');
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
    protected function _toOptionArray($valueField = 'notifications_id', $labelField = 'customer_id', $additional = [])
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }
}
