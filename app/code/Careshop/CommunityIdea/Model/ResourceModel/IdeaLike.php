<?php

namespace Careshop\CommunityIdea\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class IdeaLike extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('community_idea_like', 'like_id');
    }
}
