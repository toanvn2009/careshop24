<?php

namespace Careshop\CommunityIdea\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Careshop\CommunityIdea\Model\ResourceModel\Comment\CollectionFactory as CommentCollectionFactory;
use Careshop\CommunityIdea\Model\ResourceModel\Idea\CollectionFactory;

class Comment extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'community_comment';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'community_comment';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'community_comment';

    /**
     * @var string
     */
    protected $_idFieldName = 'comment_id';

    /**
     * Idea Collection Factory
     *
     * @var CollectionFactory
     */
    public $ideaCollectionFactory;

    /**
     * @var CommentCollectionFactory
     */
    public $commentCollectionFactory;

    /**
     * Comment constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param CollectionFactory $ideaCollectionFactory
     * @param CommentCollectionFactory $commentCollectionFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CollectionFactory $ideaCollectionFactory,
        CommentCollectionFactory $commentCollectionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->ideaCollectionFactory = $ideaCollectionFactory;
        $this->commentCollectionFactory = $commentCollectionFactory;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Comment::class);
    }

    /**
     * @inheritdoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

     /**
     * @return Collection
     */
    public function getAllCommentLikeIdeas($idea_id=null)
    {
        
            $collection = $this->commentCollectionFactory->create();
          
            $collection->join(
                ['comment_like' => $this->getResource()->getTable('community_comment_like')],
                'main_table.comment_id=comment_like.like_id AND main_table.idea_id='.$idea_id,
                ['like_id']
            );

        return $collection ;
    }
}
