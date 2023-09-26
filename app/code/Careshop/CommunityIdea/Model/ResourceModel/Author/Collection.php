<?php

namespace Careshop\CommunityIdea\Model\ResourceModel\Author;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Careshop\CommunityIdea\Model\Author;
use Careshop\CommunityIdea\Model\ResourceModel\Author as AuthorResourceModel;

class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected $_idFieldName = 'user_id';

    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_init(Author::class, AuthorResourceModel::class);
    }
}
