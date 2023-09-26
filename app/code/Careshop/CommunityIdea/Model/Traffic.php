<?php

namespace Careshop\CommunityIdea\Model;

use Magento\Framework\Model\AbstractModel;

class Traffic extends AbstractModel
{
    /**
     * Define resource model
     */
    public function _construct()
    {
        $this->_init(ResourceModel\Traffic::class);
    }
}
