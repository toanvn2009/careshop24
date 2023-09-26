<?php

namespace Careshop\CommunityIdea\Api\Data\SearchResult;

use Magento\Framework\Api\SearchResultsInterface;

interface TopicSearchResultInterface extends SearchResultsInterface
{
    /**
     * @return \Careshop\CommunityIdea\Api\Data\TopicInterface[]
     */
    public function getItems();

    /**
     * @param \Careshop\CommunityIdea\Api\Data\TopicInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
