<?php

namespace Careshop\CommunityIdea\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Traffic extends AbstractDb
{
    /**
     * Define main table
     */
    public function _construct()
    {
        $this->_init('community_idea_traffic', 'traffic_id');
    }
}
