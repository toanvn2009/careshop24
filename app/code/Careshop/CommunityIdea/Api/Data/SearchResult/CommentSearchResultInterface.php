<?php

namespace Careshop\CommunityIdea\Api\Data\SearchResult;

use Magento\Framework\Api\SearchResultsInterface;

interface CommentSearchResultInterface extends SearchResultsInterface
{
    /**
     * @return \Careshop\CommunityIdea\Api\Data\CommentInterface[]
     */
    public function getItems();

    /**
     * @param \Careshop\CommunityIdea\Api\Data\CommentInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
