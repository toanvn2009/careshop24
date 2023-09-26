<?php

namespace Careshop\CommunityTesterProduct\Model;

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
use Careshop\CommunityTesterProduct\Model\ResourceModel\TesterProduct as TesterProductResource;
use Careshop\CommunityTesterProduct\Model\ResourceModel\TesterProduct\Collection;
use Careshop\CommunityTesterProduct\Model\ResourceModel\TesterProduct\CollectionFactory as TesterProductCollectionFactory;

class TesterProduct extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'community_tester_product';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'community_tester_product';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'community_tester_product';

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
     * Idea constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param DateTime $dateTime
     * @param Data $helperData
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
        TesterProductCollectionFactory $testerProductCollectionFactory,
        ProductCollectionFactory $productCollectionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->testerProductCollectionFactory     = $testerProductCollectionFactory;
        $this->productCollectionFactory  = $productCollectionFactory;
        $this->dateTime                  = $dateTime;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(TesterProductResource::class);
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
}
