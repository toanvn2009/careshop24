<?php

namespace Careshop\CommunityIdea\Model\ResourceModel\Like;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Careshop\CommunityIdea\Model\Like;
use Careshop\CommunityIdea\Model\ResourceModel\Like as LikeResourceModel;

class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(Like::class, LikeResourceModel::class);
    }
}
