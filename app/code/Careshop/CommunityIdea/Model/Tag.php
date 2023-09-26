<?php

namespace Careshop\CommunityIdea\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Careshop\CommunityIdea\Model\ResourceModel\Idea\Collection;
use Careshop\CommunityIdea\Model\ResourceModel\Idea\CollectionFactory;
use Careshop\CommunityIdea\Model\ResourceModel\Tag\CollectionFactory as TagCollectionFactory;

/**
 * @method Tag setName($name)
 * @method Tag setDescription($description)
 * @method Tag setEnabled($enabled)
 * @method mixed getName()
 * @method mixed getDescription()
 * @method mixed getEnabled()
 * @method Tag setCreatedAt(string $createdAt)
 * @method string getCreatedAt()
 * @method Tag setUpdatedAt(string $updatedAt)
 * @method string getUpdatedAt()
 * @method Tag setIdeasData(array $data)
 * @method array getIdeasData()
 * @method Tag setIsChangedIdeaList(bool $flag)
 * @method bool getIsChangedIdeaList()
 * @method Tag setAffectedIdeaIds(array $ids)
 * @method bool getAffectedIdeaIds()
 */
class Tag extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'community_tag';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'community_tag';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'community_tag';

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
     * @var TagCollectionFactory
     */
    public $tagCollectionFactory;

    /**
     * Tag constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param CollectionFactory $ideaCollectionFactory
     * @param TagCollectionFactory $tagCollectionFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CollectionFactory $ideaCollectionFactory,
        TagCollectionFactory $tagCollectionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->ideaCollectionFactory = $ideaCollectionFactory;
        $this->tagCollectionFactory = $tagCollectionFactory;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Tag::class);
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
     * @return array|mixed
     */
    public function getIdeasPosition()
    {
        if (!$this->getId()) {
            return [];
        }

        $array = $this->getData('ideas_position');
        if (!$array) {
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
                ['idea_tag' => $this->getResource()->getTable('community_idea_tag')],
                'main_table.idea_id=idea_tag.idea_id AND idea_tag.tag_id=' . $this->getId(),
                ['position']
            );

            $this->ideaCollection = $collection;
        }

        return $this->ideaCollection;
    }
}
