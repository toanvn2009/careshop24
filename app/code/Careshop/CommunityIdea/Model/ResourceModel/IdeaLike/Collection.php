<?php

namespace Careshop\CommunityIdea\Model\ResourceModel\IdeaLike;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Careshop\CommunityIdea\Model\IdeaLike;

class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(IdeaLike::class, \Careshop\CommunityIdea\Model\ResourceModel\IdeaLike::class);
    }
}
