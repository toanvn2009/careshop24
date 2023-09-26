<?php

namespace Careshop\CommunityIdea\Model;

use Magento\Framework\Model\AbstractModel;

class IdeaLike extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'community_idea_like';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'community_idea_like';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'community_idea_like';

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
        $this->_init(ResourceModel\IdeaLike::class);
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
