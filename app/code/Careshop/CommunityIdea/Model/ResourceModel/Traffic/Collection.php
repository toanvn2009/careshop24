<?php

namespace Careshop\CommunityIdea\Model\ResourceModel\Traffic;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Careshop\CommunityIdea\Model\Traffic;
use Careshop\CommunityIdea\Model\ResourceModel\Traffic as TrafficResourceModel;

class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(Traffic::class, TrafficResourceModel::class);
    }
}
