<?php

namespace Rokanthemes\BoughtViewed\Model\ResourceModel;

class Viewed extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('product_bought_viewed', 'entity_id');
    }

}
