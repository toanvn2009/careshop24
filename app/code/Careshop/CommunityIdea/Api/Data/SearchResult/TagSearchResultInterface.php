<?php

namespace Careshop\CommunityIdea\Api\Data\SearchResult;

use Magento\Framework\Api\SearchResultsInterface;

interface TagSearchResultInterface extends SearchResultsInterface
{
    /**
     * @return \Careshop\CommunityIdea\Api\Data\TagInterface[]
     */
    public function getItems();

    /**
     * @param \Careshop\CommunityIdea\Api\Data\TagInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
