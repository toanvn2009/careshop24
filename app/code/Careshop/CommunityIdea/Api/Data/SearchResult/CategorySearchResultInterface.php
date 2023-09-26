<?php

namespace Careshop\CommunityIdea\Api\Data\SearchResult;

use Magento\Framework\Api\SearchResultsInterface;

interface CategorySearchResultInterface extends SearchResultsInterface
{
    /**
     * @return \Careshop\CommunityIdea\Api\Data\CategoryInterface[]
     */
    public function getItems();

    /**
     * @param \Careshop\CommunityIdea\Api\Data\CategoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
