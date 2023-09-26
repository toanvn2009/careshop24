<?php

namespace Careshop\CommunityIdea\Api\Data\SearchResult;

use Magento\Framework\Api\SearchResultsInterface;


interface IdeaSearchResultInterface extends SearchResultsInterface
{
    /**
     * @return \Careshop\CommunityIdea\Api\Data\IdeaInterface[]
     */
    public function getItems();

    /**
     * @param \Careshop\CommunityIdea\Api\Data\IdeaInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
