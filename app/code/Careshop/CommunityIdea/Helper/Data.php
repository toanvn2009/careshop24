<?php

namespace Careshop\CommunityIdea\Helper;

use DateTimeZone;
use Exception;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filter\TranslitUrl;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\DesignInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Careshop\CommunityIdea\Model\Author;
use Careshop\CommunityIdea\Model\AuthorFactory;
use Careshop\CommunityIdea\Model\Category;
use Careshop\CommunityIdea\Model\CategoryFactory;
use Careshop\CommunityIdea\Model\Config\Source\SideBarLR;
use Careshop\CommunityIdea\Model\Idea;
use Careshop\CommunityIdea\Model\IdeaFactory;
use Careshop\CommunityIdea\Model\IdeaHistoryFactory;
use Careshop\CommunityIdea\Model\ResourceModel\Author\Collection as AuthorCollection;
use Careshop\CommunityIdea\Model\ResourceModel\Category\Collection as CategoryCollection;
use Careshop\CommunityIdea\Model\ResourceModel\Category\CollectionFactory as CommunityCategoryCollectionFactory;
use Careshop\CommunityIdea\Model\ResourceModel\Idea\Collection as IdeaCollection;
use Careshop\CommunityIdea\Model\ResourceModel\Tag\Collection as TagCollection;
use Careshop\CommunityIdea\Model\ResourceModel\Topic\Collection;
use Careshop\CommunityIdea\Model\Tag;
use Careshop\CommunityIdea\Model\TagFactory;
use Careshop\CommunityIdea\Model\Topic;
use Careshop\CommunityIdea\Model\TopicFactory;
use Careshop\Community\Helper\AbstractData as CoreHelper;
use Careshop\CommunityCustomer\Model\ProfileFactory;

class Data extends CoreHelper
{
    const CONFIG_MODULE_PATH = 'community';
    const TYPE_IDEA = 'idea';
    const TYPE_CATEGORY = 'category';
    const TYPE_TAG = 'tag';
    const TYPE_TOPIC = 'topic';
    const TYPE_HISTORY = 'history';
    const TYPE_AUTHOR = 'author';
    const TYPE_MONTHLY = 'month';

    /**
     * @var IdeaFactory
     */
    public $ideaFactory;

    /**
     * @var CategoryFactory
     */
    public $categoryFactory;

    /**
     * @var TagFactory
     */
    public $tagFactory;

    /**
     * @var TopicFactory
     */
    public $topicFactory;

    /**
     * @var AuthorFactory
     */
    public $authorFactory;

    /**
     * @var TranslitUrl
     */
    public $translitUrl;

    /**
     * @var DateTime
     */
    public $dateTime;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var HttpContext
     */
    protected $_httpContext;

    /**
     * @var IdeaHistoryFactory
     */
    protected $ideaHistoryFactory;

    /**
     * @var ProductMetadataInterface
     */
    protected $_productMetadata;
     /**
     * @var categoriesTree
     */
    protected $categoriesTree;
    /**
     * @var CommunityCategoryCollectionFactory
     */
    public $collectionFactory;
    /**
     * @var ProfileFactory
     */
    protected $profileFactory;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param IdeaFactory $ideaFactory
     * @param CategoryFactory $categoryFactory
     * @param TagFactory $tagFactory
     * @param TopicFactory $topicFactory
     * @param AuthorFactory $authorFactory
     * @param IdeaHistoryFactory $ideaHistoryFactory
     * @param TranslitUrl $translitUrl
     * @param ProductMetadataInterface $productMetadata
     * @param Session $customerSession
     * @param HttpContext $httpContext
     * @param DateTime $dateTime
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        IdeaFactory $ideaFactory,
        CategoryFactory $categoryFactory,
        CommunityCategoryCollectionFactory $collectionFactory,
        \Magento\Customer\Model\CustomerFactory $customer,
        TagFactory $tagFactory,
        TopicFactory $topicFactory,
        AuthorFactory $authorFactory,
        ProfileFactory $profileFactory,
        IdeaHistoryFactory $ideaHistoryFactory,
        TranslitUrl $translitUrl,
        ProductMetadataInterface $productMetadata,
        Session $customerSession,
        HttpContext $httpContext,
        DateTime $dateTime
    ) {
        $this->ideaFactory = $ideaFactory;
        $this->categoryFactory = $categoryFactory;
        $this->tagFactory = $tagFactory;
        $this->topicFactory = $topicFactory;
        $this->authorFactory = $authorFactory;
        $this->profileFactory = $profileFactory;
        $this->ideaHistoryFactory = $ideaHistoryFactory;
        $this->translitUrl = $translitUrl;
        $this->dateTime = $dateTime;
        $this->customerSession = $customerSession;
        $this->customer = $customer;
        $this->_httpContext = $httpContext;
        $this->_productMetadata = $productMetadata;
        $this->collectionFactory = $collectionFactory;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @return bool
     */
    public function isEnabledReview()
    {
        $groupId = (string)$this->_httpContext->getValue(CustomerContext::CONTEXT_GROUP);

        if ($this->getConfigGeneral('is_review')
            && in_array($groupId, explode(',', $this->getConfigGeneral('review_mode')), true)
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getReviewMode()
    {
        $login = $this->_httpContext->getValue(CustomerContext::CONTEXT_AUTH);

        if (!$login
            && in_array('0', explode(',', $this->getConfigGeneral('review_mode')), true)
        ) {
            return '0';
        }

        return '1';
    }

    /**
     * @return string
     */
    public function getCurrentVersion()
    {
        return $this->_productMetadata->getVersion();
    }

    /**
     * @return int|null
     */
    public function getCurrentUser()
    {
        return $this->customerSession->getId();
    }

        /**
     * @return int|null
     */
    public function getUserData()
    {
        return $this->customerSession->getCustomerData();
    }
    /**
     * @return int|null
     */
    public function getCustomerIdByContext()
    {
        return $this->_httpContext->getValue('community_customer_id') ?: $this->customerSession->getId();
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    public function getCurrentStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @return bool
     */
    public function isLogin()
    {
        return $this->_httpContext->getValue(CustomerContext::CONTEXT_AUTH);
    }

    /**
     * @return bool
     */
    public function isAuthor()
    {
        $collection = $this->getAuthorCollection();

        return empty($collection->getSize());
    }

    /**
     * @return mixed
     */
    public function isEnabledAuthor()
    {
        if (!$this->_httpContext->getValue(CustomerContext::CONTEXT_AUTH)) {
            return false;
        }

        return $this->getCurrentAuthor() ? true : false;
    }

    /**
     * Set Customer Id in Context
     */
    public function setCustomerContextId()
    {
        $customer = $this->customerSession->getCustomerData();
        if (!$this->_httpContext->getValue('community_customer_id') && $customer) {
            $this->_httpContext->setValue('community_customer_id', $customer->getId(), 0);
        }
    }

    /**
     * @return DataObject
     */
    public function getCurrentAuthor()
    {
        $collection = $this->getAuthorCollection();

        return $collection ? $collection->getFirstItem() : null;
    }

    /**
     * @return AbstractCollection
     */
    public function getAuthorCollection()
    {
        if ($customerId = $this->_httpContext->getValue('community_customer_id')) {
            return $this->getFactoryByType('author')->create()->getCollection()
                ->addFieldToFilter('customer_id', $customerId);
        }

        return null;
    }

    /**
     * @return Image
     */
    public function getImageHelper()
    {
        return $this->objectManager->get(Image::class);
    }

    /**
     * @param $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getCommunityConfig($code, $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(self::CONFIG_MODULE_PATH . $code, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return array|mixed|string
     */
    public function getSidebarLayout($storeId = null)
    {
        $sideBarConfig = $this->getConfigValue(self::CONFIG_MODULE_PATH . '/sidebar/sidebar_left_right', $storeId);
        if ($sideBarConfig == 0) {
            return SideBarLR::LEFT;
        }

        if ($sideBarConfig == 1) {
            return SideBarLR::RIGHT;
        }

        return $sideBarConfig;
    }

    /**
     * @param $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getSeoConfig($code, $storeId = null)
    {
        return $this->getCommunityConfig('seo/' . $code, $storeId);
    }

    /**
     * @return mixed
     */
    public function showAuthorInfo()
    {
        return $this->getConfigGeneral('display_author');
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getCommunityName($store = null)
    {
        return $this->getConfigGeneral('name', $store) ?: __('Community');
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getRoute($store = null)
    {
        return $this->getConfigGeneral('url_prefix', $store) ?: 'community';
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function getUrlSuffix($store = null)
    {
        return $this->getConfigGeneral('url_suffix', $store)
            ? '.' . $this->getConfigGeneral('url_suffix', $store) : '';
    }

    /**
     * Get current theme id
     * @return mixed
     */
    public function getCurrentThemeId()
    {
        return $this->getConfigValue(DesignInterface::XML_PATH_THEME_ID);
    }

    /**
     * @param null $type
     * @param null $id
     * @param null $storeId
     *
     * @return IdeaCollection
     * @throws NoSuchEntityException
     */
    public function getIdeaCollection($type = null, $id = null, $storeId = null)
    {
        if ($id === null) {
            $id = $this->_request->getParam('id');
        }

        /** @var IdeaCollection $collection */
        $collection = $this->getIdeaList($storeId);

        switch ($type) {
            case self::TYPE_AUTHOR:
                $collection->addFieldToFilter('author_id', $id);
                break;
            case self::TYPE_CATEGORY:
                $collection->join(
                    ['category' => $collection->getTable('community_idea_category')],
                    'main_table.idea_id=category.idea_id AND category.category_id=' . $id,
                    ['position']
                );
                break;
            case self::TYPE_TAG:
                $collection->join(
                    ['tag' => $collection->getTable('community_idea_tag')],
                    'main_table.idea_id=tag.idea_id AND tag.tag_id=' . $id,
                    ['position']
                );
                break;
            case self::TYPE_TOPIC:
                $collection->join(
                    ['topic' => $collection->getTable('community_idea_topic')],
                    'main_table.idea_id=topic.idea_id AND topic.topic_id=' . $id,
                    ['position']
                );
                break;
            case self::TYPE_MONTHLY:
                $collection->addFieldToFilter('publish_date', ['like' => $id . '%']);
                break;
        }

        return $collection;
    }

    /**
     * @param null $storeId
     *
     * @return IdeaCollection
     * @throws NoSuchEntityException
     */
    public function getIdeaList($storeId = null)
    {
        /** @var IdeaCollection $collection */
        $collection = $this->getObjectList(self::TYPE_IDEA, $storeId)
            ->addFieldToFilter('publish_date', ['to' => $this->dateTime->date()])
            ->setOrder('publish_date', 'desc');
        return $collection;
    }

    /**
     * @param $array
     *
     * @return \Magento\Sales\Model\ResourceModel\Collection\AbstractCollection
     */
    public function getCategoryCollection($array)
    {
        try {
            $collection = $this->getObjectList(self::TYPE_CATEGORY)
                ->addFieldToFilter('category_id', ['in' => $array]);

            return $collection;
        } catch (Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }

        return null;
    }

    /**
     * Get object collection (Category, Tag, Idea, Topic)
     *
     * @param null $type
     * @param null $storeId
     *
     * @return AuthorCollection|CategoryCollection|IdeaCollection|TagCollection|Collection
     * @throws NoSuchEntityException
     */
    public function getObjectList($type = null, $storeId = null)
    {
        /** @var AuthorCollection|CategoryCollection|IdeaCollection|TagCollection|Collection $collection */
        $collection = $this->getFactoryByType($type)
            ->create()
            ->getCollection()
            ->addFieldToFilter('enabled', 1);

        return $collection;
    }

    /**
     * @param $collection
     * @param null $storeId
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function addStoreFilter($collection, $storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        $collection->addFieldToFilter('store_ids', [
            ['finset' => Store::DEFAULT_STORE_ID],
            ['finset' => $storeId]
        ]);

        return $collection;
    }

    /**
     * @param $idea
     * @param bool $modify
     *
     * @return Author
     */
    public function getAuthorByIdea($idea, $modify = false)
    {
        $author = $this->customer->create();

        $authorId = $idea->getCustomerId();
        if ($authorId) {
            $author->load($idea->getCustomerId());
            return $author;
        }
        return false;
    }

    /**
     * @param null $urlKey
     * @param null $type
     * @param null $store
     *
     * @return string
     */
    public function getCommunityUrl($urlKey = null, $type = null, $store = null)
    {
        if (is_object($urlKey)) {
            $urlKey = $urlKey->getUrlKey();
        }

        $urlKey = ($type ? $type . '/' : '') . $urlKey;
        $url = $this->getUrl($this->getRoute($store) . '/' . $urlKey);
        $url = explode('?', $url);
        $url = $url[0];

        return rtrim($url, '/') . $this->getUrlSuffix($store);
    }

    /**
     * @param $value
     * @param null $code
     * @param null $type
     *
     * @return Author|Category|Idea|Tag|Topic
     */
    public function getObjectByParam($value, $code = null, $type = null)
    {
        $object = $this->getFactoryByType($type)
            ->create()
            ->load($value, $code);

        return $object;
    }

    /**
     * @param $type
     *
     * @return AuthorFactory|CategoryFactory|IdeaFactory|TagFactory|TopicFactory
     */
    public function getFactoryByType($type = null)
    {
        switch ($type) {
            case self::TYPE_CATEGORY:
                $object = $this->categoryFactory;
                break;
            case self::TYPE_TAG:
                $object = $this->tagFactory;
                break;
            case self::TYPE_AUTHOR:
                $object = $this->authorFactory;
                break;
            case self::TYPE_TOPIC:
                $object = $this->topicFactory;
                break;
            case self::TYPE_HISTORY:
                $object = $this->ideaHistoryFactory;
                break;
            default:
                $object = $this->ideaFactory;
        }

        return $object;
    }

    /**
     * Generate url_key for post, tag, topic, category, author
     *
     * @param $resource
     * @param $object
     * @param $name
     *
     * @return string
     * @throws LocalizedException
     */
    public function generateUrlKey($resource, $object, $name)
    {
        $attempt = -1;
        do {
            if ($attempt++ >= 10) {
                throw new LocalizedException(__('Unable to generate url key. Please check the setting and try again.'));
            }

            $urlKey = $this->translitUrl->filter($name);
            if ($urlKey) {
                $urlKey .= ($attempt ?: '');
            }
        } while ($this->checkUrlKey($resource, $object, $urlKey));

        return $urlKey;
    }

    /**
     * @param $resource
     * @param $object
     * @param $urlKey
     *
     * @return bool
     */
    public function checkUrlKey($resource, $object, $urlKey)
    {
        if (empty($urlKey)) {
            return true;
        }

        $adapter = $resource->getConnection();
        $select = $adapter->select()
            ->from($resource->getMainTable(), '*')
            ->where('url_key = :url_key');

        $binds = ['url_key' => (string)$urlKey.'--'.$object->getId()];
        if ($id = $object->getId()) {
            $select->where($resource->getIdFieldName() . ' != :object_id');
            $binds['object_id'] = (int)$id;
        }

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * get date formatted
     *
     * @param $date
     * @param bool $monthly
     *
     * @return false|string
     * @throws Exception
     */
    public function getDateFormat($date, $monthly = false)
    {
        $dateTime = new \DateTime($date, new DateTimeZone('UTC'));
        $dateTime->setTimezone(new DateTimeZone($this->getTimezone()));

        $dateType = $this->getCommunityConfig($monthly ? 'monthly_archive/date_type_monthly' : 'general/date_type');

        return $dateTime->format($dateType);
    }

    /**
     * get configuration zone
     * @return mixed
     */
    public function getTimezone()
    {
        return $this->getConfigValue('general/locale/timezone');
    }

    /**
     * @param $route
     * @param array $params
     *
     * @return string
     */
    public function getUrl($route, $params = [])
    {
        return $this->_urlBuilder->getUrl($route, $params);
    }

    /**
     * @param $object
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function checkStore($object)
    {
        $storeEnable = explode(',', $object->getStoreIds());

        return in_array('0', $storeEnable, true)
            || in_array((string)$this->storeManager->getStore()->getId(), $storeEnable, true);
    }

    /**
     * @return Array
     */
    public function getCategoriesArray()
    { 
        if ($this->categoriesTree === null) {
            /* @var $collection \Magento\Catalog\Model\ResourceModel\Category\Collection */
            $collection = $this->collectionFactory->create();
            $arrayElements = array();
            $element = array();
            foreach ($collection as $category) {
             
                $elemnt[$category->getId()] = array(
                    'parent_id' => $category->getParentId(),
                    'value' => $category->getId(),
                    'is_active' => $category->getData('enabled'),
                    'label' =>  $category->getName(),
                );
                $arrayElements = $elemnt;
            }
            $this->categoriesTree = $arrayElements;
        }
       // echo "<pre>"; print_r($this->categoriesTree ); echo "</pre>";
        return $this->categoriesTree ;
    }
    /**
     * @return mixed
     */
    public function getCategoriesTree()
    {   
        $categories = $this->getCategoriesArray(); 
      
        $tree = $this->buildTree($categories);
        return $tree; 
    }

    function buildTree(&$array) {
        $tree = array();
    
        // Create an associative array with each key being the ID of the item
        foreach($array as $k => &$v) {
          $tree[$v['value']] = &$v;
        }
    
        // Loop over the array and add each child to their parent
        foreach($tree as $k => &$v) {
            if(!$v['parent_id'] || $v['parent_id'] ==0) {
              continue;
            }
            $tree[$v['parent_id']]['optgroup'][] = &$v;
        }
    
        // Loop over the array again and remove any items that don't have a parent of 0;
        foreach($tree as $k => &$v) {
          if(!$v['parent_id'] || $v['parent_id'] ==0) {
            continue;
          }
          unset($tree[$k]);
        }
    
        return $tree;
    }

    public function getTopics( $idea_id = null ) 
    {
        $topic = $this->topicFactory->create();
        return $topic->getTopicIdeas($idea_id);
    }

    public function getTopicById($topic_id=null)
    {
        if($topic_id) {
            $topic = $this->topicFactory->create();
            $topic->load($topic_id); 
            return $topic;
        }else {
            return array();
        }
    }

    /**
     * @param $customer_id
     *
     * @return int
     */
    public function getCommentinTopic($customer_id)
    {
        $cmt = $this->topicFactory->create()->getCollection()
                    ->addFieldToFilter('customer_id', $customer_id);

        return $cmt->count();
    }

    public function getAuthorById($author_id=null)
    {
        if($author_id) {
            $author = $this->authorFactory->create();
            $author->load($author_id);
            return $author;        
        } else {
            return array();
        }
    }

    public function getAllAuthor()
    {
        $author = $this->authorFactory->create()->getCollection();
        
        if($author->count()){
                return $author;      
          
        } else {
            return array();
        }
    }

    public function getAuthorByCustomerId($customer_id = null)
    { 
        $author = $this->customer->create();
        if ($customer_id) {
            $author->load($customer_id);
            return $author;
        }
        return false;
    }

    public function getCustomerProfile($customer_id)
    {
      
        if($customer_id) {
            $profile = $this->profileFactory->create();
            $profile = $profile->getCollection()
                     ->addFieldToFilter('customer_id',$customer_id);
            if($profile){
                return  $profile->getFirstItem();
            }       
        } else {
            return array();
        }    
    }
    public function getMediaUrl() 
    {
        return  $this ->storeManager-> getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
    }

    public function getIdeaByAuthorId($author_id=null)
    {
       // echo 'author:'.$author_id;
        $idea = $this->ideaFactory->create()->getCollection()
                     ->addFieldToFilter('author_id',$author_id);
        if($idea->count()){
                return  $idea->getLastItem();      
        } else {
            return array();
        }                         
    }
    public function makeUrlKey($name)
    {
        $urlKey = '';
        $urlKey = $this->translitUrl->filter($name);
        if ($urlKey) {
            return $urlKey;
        }
        return $urlKey;
    }
}
