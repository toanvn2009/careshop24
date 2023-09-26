<?php

namespace Careshop\CommunityIdea\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Careshop\CommunityIdea\Model\ResourceModel\Idea\Collection;
use Careshop\CommunityIdea\Model\ResourceModel\Idea\CollectionFactory;
use Careshop\CommunityIdea\Model\ResourceModel\Topic\CollectionFactory as TopicCollectionFactory;

/**
 * @method Topic setName($name)
 * @method Topic setDescription($description)
 * @method Topic setEnabled($enabled)
 * @method Topic setUrlKey($urlKey)
 * @method Topic setMetaTitle($metaTitle)
 * @method Topic setMetaDescription($metaDescription)
 * @method Topic setMetaKeywords($metaKeywords)
 * @method Topic setMetaRobots($metaRobots)
 * @method mixed getName()
 * @method mixed getDescription()
 * @method mixed getEnabled()
 * @method mixed getUrlKey()
 * @method mixed getMetaTitle()
 * @method mixed getMetaDescription()
 * @method mixed getMetaKeywords()
 * @method mixed getMetaRobots()
 * @method Topic setCreatedAt(string $createdAt)
 * @method string getCreatedAt()
 * @method Topic setUpdatedAt(string $updatedAt)
 * @method string getUpdatedAt()
 * @method Topic setIdeasData(array $data)
 * @method array getIdeasData()
 * @method Topic setIsChangedIdeaList(bool $flag)
 * @method bool getIsChangedIdeaList()
 * @method Topic setAffectedIdeaIds(array $ids)
 * @method bool getAffectedIdeaIds()
 */
class Topic extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'community_topic';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'community_topic';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'community_topic';

    /**
     * Idea Collection
     *
     * @var Collection
     */
    public $ideaCollection;

    /**
     * Idea Collection Factory
     *
     * @var CollectionFactory
     */
    public $ideaCollectionFactory;

    /**
     * Topic Collection Factory
     *
     * @var TopicCollectionFactory
     */
    public $topicCollectionFactory;

    /**
     * Topic constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param CollectionFactory $ideaCollectionFactory
     * @param TopicCollectionFactory $topicCollectionFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CollectionFactory $ideaCollectionFactory,
        TopicCollectionFactory $topicCollectionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->ideaCollectionFactory = $ideaCollectionFactory;
        $this->topicCollectionFactory = $topicCollectionFactory;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Topic::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * get entity default values
     *
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];
        $values['enabled'] = '1';
        $values['store_ids'] = '1';

        return $values;
    }

    /**
     * @return array|mixed
     */
    public function getIdeasPosition()
    {
        if (!$this->getId()) {
            return [];
        }
        $array = $this->getData('ideas_position');
        if ($array === null) {
            $array = $this->getResource()->getIdeasPosition($this);
            $this->setData('ideas_position', $array);
        }

        return $array;
    }

    /**
     * @return Collection
     */
    public function getSelectedIdeasCollection()
    {
        if ($this->ideaCollection === null) {
            $collection = $this->ideaCollectionFactory->create();
            $collection->join(
                ['topic' => $this->getResource()->getTable('community_idea_topic')],
                'main_table.idea_id=topic.idea_id AND topic.topic_id=' . $this->getId(),
                ['position']
            );
            $this->ideaCollection = $collection;
        }

        return $this->ideaCollection;
    }
    /**
     * @return Collection
     */
    public function getTopicIdeas( $idea_id = null )
    {
        $collection = $this->topicCollectionFactory->create();
        $collection = $collection->addFieldToFilter('idea_id', $idea_id); 
        return $collection ;
    }
}
