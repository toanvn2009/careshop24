<?php
// @codingStandardsIgnoreFile

namespace Careshop\CommunityCustomer\Model;

class Avatar extends \Magento\Framework\Model\AbstractModel
{
   
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Careshop\CommunityCustomer\Model\ResourceModel\Avatar');
    }
}
