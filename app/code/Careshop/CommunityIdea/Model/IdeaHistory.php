<?php

namespace Careshop\CommunityIdea\Model;

use Magento\Framework\Model\AbstractModel;
use Careshop\CommunityIdea\Helper\Data;

class IdeaHistory extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'community_idea_history';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'community_idea_history';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'community_idea_history';

    /**
     * @var string
     */
    protected $_idFieldName = 'like_id';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\IdeaHistory::class);
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @param $ideaId
     *
     * @return int
     */
    public function getSumIdeaHistory($ideaId)
    {
        return $this->getCollection()->addFieldToFilter('idea_id', $ideaId)->count();
    }

    /**
     * @param $ideaId
     */
    public function removeFirstHistory($ideaId)
    {
        $this->getCollection()->addFieldToFilter('idea_id', $ideaId)->getFirstItem()->delete();
    }

    /**
     * @return array|mixed
     */
    public function getProductsPosition()
    {
        if (!$this->getId()) {
            return [];
        }
        $data = [];
        foreach (Data::jsonDecode($this->getProductIds()) as $key => $value) {
            $data[$key] = $value['position'];
        }

        return $data;
    }
}
