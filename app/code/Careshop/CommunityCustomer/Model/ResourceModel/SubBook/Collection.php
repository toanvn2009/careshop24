<?php
namespace Careshop\CommunityCustomer\Model\ResourceModel\SubBook;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
     protected $_idFieldName = 'subbook_id';

    /**
     * Define resource model
     *
     * @return void
     */
	protected function _construct()
    {
        $this->_init(
            \Careshop\CommunityCustomer\Model\SubBook::class,
            \Careshop\CommunityCustomer\Model\ResourceModel\SubBook::class
        );
    }
 
    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }
	
	
}