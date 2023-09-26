<?php

namespace Careshop\CommunityIdea\Model\ResourceModel\Comment;

use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;
use Careshop\CommunityIdea\Api\Data\SearchResult\CommentSearchResultInterface;
use Careshop\CommunityIdea\Model\Comment;

class Collection extends AbstractCollection implements CommentSearchResultInterface
{
    /**
     * @var string
     */
    protected $_idFieldName = 'comment_id';

    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(Comment::class, \Careshop\CommunityIdea\Model\ResourceModel\Comment::class);
    }
}
