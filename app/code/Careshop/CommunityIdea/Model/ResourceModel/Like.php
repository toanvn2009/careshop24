<?php

namespace Careshop\CommunityIdea\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Like extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('community_comment_like', 'like_id');
    }
}
