<?php

namespace Careshop\CommunityIdea\Model;

use Exception;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Careshop\CommunityIdea\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Careshop\CommunityIdea\Model\ResourceModel\Idea\Collection;
use Careshop\CommunityIdea\Model\ResourceModel\Idea\CollectionFactory;

/**
 * @method Category setName($name)
 * @method Category setDescription($description)
 * @method Category setUrlKey($urlKey)
 * @method Category setEnabled($enabled)
 * @method Category setMetaTitle($metaTitle)
 * @method Category setMetaDescription($metaDescription)
 * @method Category setMetaKeywords($metaKeywords)
 * @method Category setMetaRobots($metaRobots)
 * @method mixed getName()
 * @method mixed getDescription()
 * @method mixed getUrlKey()
 * @method mixed getEnabled()
 * @method mixed getMetaTitle()
 * @method mixed getMetaDescription()
 * @method mixed getMetaKeywords()
 * @method mixed getMetaRobots()
 * @method Category setParentId(int $parentId)
 * @method int getParentId()
 * @method Category setPath(string $path)
 * @method string getPath()
 * @method Category setPosition(int $path)
 * @method int getPosition()
 * @method Category setChildrenCount(int $path)
 * @method int getChildrenCount()
 * @method Category setCreatedAt(string $createdAt)
 * @method string getCreatedAt()
 * @method Category setUpdatedAt(string $updatedAt)
 * @method string getUpdatedAt()
 * @method Category setMovedCategoryId(string $id)
 * @method Category setAffectedCategoryIds(array $ids)
 * @method Category setIdeasData(array $data)
 * @method array getIdeasData()
 * @method Category setIsChangedIdeaList(bool $flag)
 * @method bool getIsChangedIdeaList()
 * @method Category setAffectedIdeaIds(array $ids)
 * @method bool getAffectedIdeaIds()
 */
class Category extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'community_category';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'community_category';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'community_category';

    /**
     * Idea Collection
     *
     * @var Collection
     */
    public $ideaCollection;

    /**
     * Community Category Factory
     *
     * @var CategoryFactory
     */
    public $categoryFactory;

    /**
     * Idea Collection Factory
     *
     * @var CollectionFactory
     */
    public $ideaCollectionFactory;

    /**
     * @var CategoryCollectionFactory
     */
    public $categoryCollectionFactory;

    /**
     * Category constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param CategoryFactory $categoryFactory
     * @param CollectionFactory $ideaCollectionFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CategoryFactory $categoryFactory,
        CollectionFactory $ideaCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->ideaCollectionFactory = $ideaCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Category::class);
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
        $values['store_ids'] = '1';
        $values['enabled'] = '1';

        return $values;
    }

    /**
     * get tree path ids
     *
     * @return array
     */
    public function getPathIds()
    {
        $ids = $this->getData('path_ids');
        if ($ids === null) {
            $ids = explode('/', $this->getPath());
            $this->setData('path_ids', $ids);
        }

        return $ids;
    }

    /**
     * get all parent ids
     *
     * @return array
     */
    public function getParentIds()
    {
        return array_diff($this->getPathIds(), [$this->getId()]);
    }

    /**
     * move Community Category in tree
     *
     * @param $parentId
     * @param $afterCategoryId
     *
     * @return $this
     * @throws LocalizedException
     * @throws Exception
     */
    public function move($parentId, $afterCategoryId)
    {
        try {
            $parent = $this->categoryFactory->create()->load($parentId);
        } catch (NoSuchEntityException $e) {
            throw new LocalizedException(
                __('Sorry, but we can\'t move the Community Category because we can\'t find the new parent Community Category you selected.'),
                $e
            );
        }

        if (!$this->getId()) {
            throw new LocalizedException(
                __('Sorry, but we can\'t move the Community Category because we can\'t find the new parent Community Category you selected.')
            );
        }
        if ($parent->getId() == $this->getId()) {
            throw new LocalizedException(
                __('We can\'t perform this Community Category move operation because the parent Community Category matches the child Community Category.')
            );
        }

        $this->setMovedCategoryId($this->getId());
        $oldParentId = $this->getParentId();

        $eventParams = [
            $this->_eventObject => $this,
            'parent' => $parent,
            'category_id' => $this->getId(),
            'prev_parent_id' => $oldParentId,
            'parent_id' => $parentId,
        ];

        $this->_getResource()->beginTransaction();
        try {
            $this->_eventManager->dispatch($this->_eventPrefix . '_move_before', $eventParams);
            $this->getResource()->changeParent($this, $parent, $afterCategoryId);
            $this->_eventManager->dispatch($this->_eventPrefix . '_move_after', $eventParams);
            $this->_getResource()->commit();

            // Set data for indexer
            $this->setAffectedCategoryIds([$this->getId(), $oldParentId, $parentId]);
        } catch (Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }
        $this->_eventManager->dispatch($this->_eventPrefix . '_move', $eventParams);
        $this->_cacheManager->clean([self::CACHE_TAG]);

        return $this;
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
        if (!$this->ideaCollection) {
            $collection = $this->ideaCollectionFactory->create();
            $collection->join(
                ['cat' => $this->getResource()->getTable('community_idea_category')],
                'main_table.idea_id=cat.idea_id AND cat.category_id=' . $this->getId(),
                ['position']
            );
            $this->ideaCollection = $collection;
        }

        return $this->ideaCollection;
    }
}
