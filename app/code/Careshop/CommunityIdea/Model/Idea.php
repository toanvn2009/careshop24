<?php

namespace Careshop\CommunityIdea\Model;

use Exception;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime;
use Careshop\CommunityIdea\Helper\Data;
use Careshop\CommunityIdea\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Careshop\CommunityIdea\Model\ResourceModel\Idea as IdeaResource;
use Careshop\CommunityIdea\Model\ResourceModel\Idea\Collection;
use Careshop\CommunityIdea\Model\ResourceModel\Idea\CollectionFactory as IdeaCollectionFactory;
use Careshop\CommunityIdea\Model\ResourceModel\Tag\CollectionFactory;
use Careshop\CommunityIdea\Model\ResourceModel\Topic\CollectionFactory as TopicCollectionFactory;

/**
 * @method Idea setName($name)
 * @method Idea setShortDescription($shortDescription)
 * @method Idea setIdeaContent($ideaContent)
 * @method Idea setImage($image)
 * @method Idea setViews($views)
 * @method Idea setEnabled($enabled)
 * @method Idea setUrlKey($urlKey)
 * @method Idea setInRss($inRss)
 * @method Idea setAllowComment($allowComment)
 * @method Idea setMetaTitle($metaTitle)
 * @method Idea setMetaDescription($metaDescription)
 * @method Idea setMetaKeywords($metaKeywords)
 * @method Idea setMetaRobots($metaRobots)
 * @method mixed getName()
 * @method mixed getIdeaContent()
 * @method mixed getImage()
 * @method mixed getViews()
 * @method mixed getEnabled()
 * @method mixed getUrlKey()
 * @method mixed getInRss()
 * @method mixed getAllowComment()
 * @method mixed getMetaTitle()
 * @method mixed getMetaDescription()
 * @method mixed getMetaKeywords()
 * @method mixed getMetaRobots()
 * @method Idea setCreatedAt(string $createdAt)
 * @method string getCreatedAt()
 * @method Idea setUpdatedAt(string $updatedAt)
 * @method string getUpdatedAt()
 * @method Idea setTagsData(array $data)
 * @method Idea setTopicsData(array $data)
 * @method Idea setProductsData(array $data)
 * @method array getTagsData()
 * @method array getProductsData()
 * @method array getTopicsData()
 * @method Idea setIsChangedTagList(bool $flag)
 * @method Idea setIsChangedProductList(bool $flag)
 * @method Idea setIsChangedTopicList(bool $flag)
 * @method Idea setIsChangedCategoryList(bool $flag)
 * @method bool getIsChangedTagList()
 * @method bool getIsChangedTopicList()
 * @method bool getIsChangedCategoryList()
 * @method Idea setAffectedTagIds(array $ids)
 * @method Idea setAffectedEntityIds(array $ids)
 * @method Idea setAffectedTopicIds(array $ids)
 * @method Idea setAffectedCategoryIds(array $ids)
 * @method bool getAffectedTagIds()
 * @method bool getAffectedTopicIds()
 * @method bool getAffectedCategoryIds()
 * @method array getCategoriesIds()
 * @method Idea setCategoriesIds(array $categoryIds)
 * @method array getTagsIds()
 * @method Idea setTagsIds(array $tagIds)
 * @method array getTopicsIds()
 * @method Idea setTopicsIds(array $topicIds)
 */
class Idea extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'community_idea';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'community_idea';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'community_idea';

    /**
     * Tag Collection
     *
     * @var ResourceModel\Tag\Collection
     */
    public $tagCollection;

    /**
     * Topic Collection
     *
     * @var ResourceModel\Topic\Collection
     */
    public $topicCollection;

    /**
     * Community Category Collection
     *
     * @var ResourceModel\Category\Collection
     */
    public $categoryCollection;

    /**
     * Tag Collection Factory
     *
     * @var CollectionFactory
     */
    public $tagCollectionFactory;

    /**
     * Topic Collection Factory
     *
     * @var TopicCollectionFactory
     */
    public $topicCollectionFactory;

    /**
     * Community Category Collection Factory
     *
     * @var CategoryCollectionFactory
     */
    public $categoryCollectionFactory;

    /**
     * Idea Collection Factory
     *
     * @var IdeaCollectionFactory
     */
    public $ideaCollectionFactory;

    /**
     * Related Idea Collection
     *
     * @var Collection
     */
    public $relatedIdeaCollection;

    /**
     * Previous Idea Collection
     *
     * @var Collection
     */
    public $prevIdeaCollection;

    /**
     * Next Idea Collection
     *
     * @var Collection
     */
    public $nextIdeaCollection;

    /**
     * @var DateTime
     */
    public $dateTime;

    /**
     * @var Data
     */
    public $helperData;

    /**
     * @var ProductCollectionFactory
     */
    public $productCollectionFactory;

    /**
     * @var ProductCollection
     */
    public $productCollection;

    /**
     * @var TrafficFactory
     */
    protected $trafficFactory;

    /**
     * Idea constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param DateTime $dateTime
     * @param Data $helperData
     * @param TrafficFactory $trafficFactory
     * @param CollectionFactory $tagCollectionFactory
     * @param TopicCollectionFactory $topicCollectionFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param IdeaCollectionFactory $ideaCollectionFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        DateTime $dateTime,
        Data $helperData,
        TrafficFactory $trafficFactory,
        CollectionFactory $tagCollectionFactory,
        TopicCollectionFactory $topicCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        IdeaCollectionFactory $ideaCollectionFactory,
        ProductCollectionFactory $productCollectionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->tagCollectionFactory      = $tagCollectionFactory;
        $this->topicCollectionFactory    = $topicCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->ideaCollectionFactory     = $ideaCollectionFactory;
        $this->productCollectionFactory  = $productCollectionFactory;
        $this->helperData                = $helperData;
        $this->dateTime                  = $dateTime;
        $this->trafficFactory            = $trafficFactory;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(IdeaResource::class);
    }

    /**
     * @inheritdoc
     */
    public function afterSave()
    {
        if ($this->isObjectNew()) {
            $trafficModel = $this->trafficFactory->create()
                ->load($this->getId(), 'idea_id');
            if (!$trafficModel->getId()) {
                $trafficModel->setData([
                    'idea_id'      => $this->getId(),
                    'numbers_view' => 0
                ])->save();
            }
        }

        return parent::afterSave();
    }

    /**
     * @param bool $shorten
     *
     * @return mixed|string
     */
    public function getShortDescription($shorten = false)
    {
        $shortDescription = $this->getData('short_description');

        $maxLength = 200;
        if ($shorten && strlen($shortDescription) > $maxLength) {
            $shortDescription = substr($shortDescription, 0, $maxLength) . '...';
        }

        return $shortDescription;
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getUrl($store = null)
    {
        return $this->helperData->getCommunityUrl($this, Data::TYPE_IDEA, $store);
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
        $values                  = [];
        $values['in_rss']        = '1';
        $values['enabled']       = '1';
        $values['allow_comment'] = '1'; 
        return $values;
    }

    /**
     * @return ResourceModel\Tag\Collection
     */
    public function getSelectedTagsCollection()
    {
        if ($this->tagCollection === null) {
            $collection = $this->tagCollectionFactory->create();
            $collection->getSelect()->join(
                $this->getResource()->getTable('community_idea_tag'),
                'main_table.tag_id=' . $this->getResource()->getTable('community_idea_tag') . '.tag_id AND '
                . $this->getResource()->getTable('community_idea_tag') . '.idea_id=' . $this->getId(),
                ['position']
            )->where("main_table.enabled='1'");
            $this->tagCollection = $collection;
        }

        return $this->tagCollection;
    }

    /**
     * @return ResourceModel\Topic\Collection
     */
    public function getSelectedTopicsCollection()
    {
        if ($this->topicCollection === null) {
            $collection = $this->topicCollectionFactory->create();
            $collection->join(
                $this->getResource()->getTable('community_idea_topic'),
                'main_table.topic_id=' . $this->getResource()->getTable('community_idea_topic') . '.topic_id AND '
                . $this->getResource()->getTable('community_idea_topic') . '.idea_id=' . $this->getId(),
                ['position']
            );
            $this->topicCollection = $collection;
        }

        return $this->topicCollection;
    }

    /**
     * @return ResourceModel\Category\Collection
     */
    public function getSelectedCategoriesCollection()
    {
        if ($this->categoryCollection === null) {
            $collection = $this->categoryCollectionFactory->create();
            $collection->join(
                $this->getResource()->getTable('community_idea_category'),
                'main_table.category_id=' . $this->getResource()->getTable('community_idea_category') .
                '.category_id AND ' . $this->getResource()->getTable('community_idea_category') . '.idea_id="'
                . $this->getId() . '"',
                ['position']
            );
            $this->categoryCollection = $collection;
        }

        return $this->categoryCollection;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getCategoryIds()
    {
        if (!$this->hasData('category_ids')) {
            $ids = $this->_getResource()->getCategoryIds($this);
            $this->setData('category_ids', $ids);
        }

        return (array) $this->_getData('category_ids');
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getTagIds()
    {
        if (!$this->hasData('tag_ids')) {
            $ids = $this->_getResource()->getTagIds($this);

            $this->setData('tag_ids', $ids);
        }

        return (array) $this->_getData('tag_ids');
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getTopicIds()
    {
        if (!$this->hasData('topic_ids')) {
            $ids = $this->_getResource()->getTopicIds($this);

            $this->setData('topic_ids', $ids);
        }

        return (array) $this->_getData('topic_ids');
    }

    /**
     * @return int
     * @throws LocalizedException
     */
    public function getViewTraffic()
    {
        if (!$this->hasData('view_traffic')) {
            $traffic = $this->_getResource()->getViewTraffic($this);

            $this->setData('view_traffic', $traffic[0]);
        }

        return $this->_getData('view_traffic');
    }

    /**
     * @return int
     * @throws LocalizedException
     */
    public function getAuthorName()
    {
        if (!$this->hasData('author_name')) {
            $author = $this->_getResource()->getAuthor($this);

            $this->setData('author_name', $author['name']);
        }

        return $this->_getData('author_name');
    }

    /**
     * @return int
     * @throws LocalizedException
     */
    public function getAuthorUrl()
    {
        if (!$this->hasData('author_url')) {
            $author = $this->_getResource()->getAuthor($this);

            $this->setData('author_url', $this->helperData->getCommunityUrl($author['url_key'], Data::TYPE_AUTHOR));
        }

        return $this->_getData('author_url');
    }

    /**
     * @return int
     * @throws LocalizedException
     */
    public function getAuthorUrlKey()
    {
        if (!$this->hasData('author_url_key')) {
            $author = $this->_getResource()->getAuthor($this);

            $this->setData('author_url_key', $author['url_key']);
        }

        return $this->_getData('author_url_key');
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getUrlImage()
    {
        $imageHelper = $this->helperData->getImageHelper();
        $imageFile   = $this->getImage() ? $imageHelper->getMediaPath($this->getImage(), 'idea') : '';
        $imageUrl    = $imageFile ? $this->helperData->getImageHelper()->getMediaUrl($imageFile) : '';

        $this->setData('image', $imageUrl);

        return $this->_getData('image');
    }

    /**
     * @throws Exception
     */
    public function updateViewTraffic()
    {
        if ($this->getId()) {
            $trafficModel = $this->trafficFactory->create()->load($this->getId(), 'idea_id');

            if ($trafficModel->getId()) {
                $trafficModel->setNumbersView($trafficModel->getNumbersView() + 1);
                $trafficModel->save();
            } else {
                $traffic = $this->trafficFactory->create();
                $traffic->addData(['idea_id' => $this->getId(), 'numbers_view' => 1])->save();
            }
        }
    }

    /**
     * @return Collection|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getRelatedIdeasCollection()
    {
        $topicIds = $this->_getResource()->getTopicIds($this);
        if (count($topicIds)) {
            $collection = $this->ideaCollectionFactory->create();
            $collection->getSelect()
                ->join(
                    ['topic' => $this->getResource()->getTable('community_idea_topic')],
                    'main_table.idea_id=topic.idea_id AND topic.idea_id != "' . $this->getId()
                    . '" AND topic.topic_id IN (' . implode(',', $topicIds) . ')',
                    ['position']
                )->group('main_table.idea_id');

            if ($limit = (int) $this->helperData->getCommunityConfig('general/related_idea')) {
                $collection->getSelect()
                    ->limit($limit);
            }
            $collection->addFieldToFilter('enabled', '1');
            $this->helperData->addStoreFilter($collection);

            return $collection;
        }

        return null;
    }

    /**
     * @return ProductCollection
     */
    public function getSelectedProductsCollection()
    {
        if ($this->productCollection === null) {
            $collection = $this->productCollectionFactory->create();
            $collection->getSelect()->join(
                $this->getResource()->getTable('community_idea_product'),
                'e.entity_id=' . $this->getResource()->getTable('community_idea_product')
                . '.entity_id AND ' . $this->getResource()->getTable('community_idea_product') . '.idea_id='
                . $this->getId(),
                ['position']
            );
            $this->productCollection = $collection;
        }

        return $this->productCollection;
    }

    /**
     * @return array|mixed
     */
    public function getProductsPosition()
    {
        if (!$this->getId()) {
            return [];
        }
        $array = $this->getData('products_position');
        if ($array === null) {
            $array = $this->getResource()->getProductsPosition($this);
            $this->setData('products_position', $array);
        }

        return $array;
    }

    /**
     * get previous idea
     * @return Collection
     */
    public function getPrevIdea()
    {
        if ($this->prevIdeaCollection === null) {
            $collection = $this->ideaCollectionFactory->create();
            $collection->addFieldToFilter('idea_id', ['lt' => $this->getId()])
                ->setOrder('idea_id', 'DESC')->setPageSize(1)->setCurPage(1);
            $this->prevIdeaCollection = $collection;
        }

        return $this->prevIdeaCollection;
    }

    /**
     * get next idea
     * @return Collection
     */
    public function getNextIdea()
    {
        if ($this->nextIdeaCollection === null) {
            $collection = $this->ideaCollectionFactory->create();
            $collection->addFieldToFilter('idea_id', ['gt' => $this->getId()])
                ->setOrder('idea_id', 'ASC')->setPageSize(1)->setCurPage(1);
            $this->nextIdeaCollection = $collection;
        }

        return $this->nextIdeaCollection;
    }
}
