<?php
namespace Careshop\CommunityCustomer\Model\ResourceModel\Settings;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
     protected $_idFieldName = 'setting_id';

    /**
     * Define resource model
     *
     * @return void
     */
	protected function _construct()
    {
        $this->_init(
            \Careshop\CommunityCustomer\Model\Settings::class,
            \Careshop\CommunityCustomer\Model\ResourceModel\Settings::class
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