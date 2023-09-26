<?php

namespace Careshop\CommunityDevelopProduct\Block;

use DateTimeZone;
use Exception;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Theme\Block\Html\Pager;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Careshop\CommunityDevelopProduct\Model\ResourceModel\Forum\Collection;
use Careshop\CommunityDevelopProduct\Model\ForumFactory;
use Careshop\CommunityDevelopProduct\Model\CommentForumFactory;
use Careshop\CommunityDevelopProduct\Model\DevelopFactory;
use Careshop\Community\Helper\AbstractData as CoreHelper;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context as CustomerContext;
use Careshop\CommunityCustomer\Model\ProfileFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;

class Listforum extends Template
{

    const CONFIG_MODULE_PATH = 'community';
    protected $_registry;
    protected $profileFactory;
    protected $forumFactory;
    protected $developFactory;
        
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Customer $customers,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        ForumFactory $forumFactory,
        CommentForumFactory $commentForumFactory,
        DevelopFactory $developFactory,
        CoreHelper $coreHelper,
        ProfileFactory $profileFactory,
        HttpContext $httpContext,
        array $data = []
    ) {        
        $this->_registry = $registry;
        $this->forumFactory = $forumFactory;
        $this->commentForumFactory = $commentForumFactory;
        $this->developFactory = $developFactory;
        $this->_productloader = $_productloader;
        $this->coreHelper = $coreHelper;
        $this->_httpContext = $httpContext;
        $this->profileFactory  = $profileFactory;
        $this->_customers = $customers;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager  = $storeManager;
        parent::__construct($context, $data);
    }

    public function getResourceForum()
    {
        $resource = $this->forumFactory->create()->getResource();
        return $resource;
    }

    public function getConfigValue($field, $scopeValue = null, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue($field, $scopeType, $scopeValue);
    }

    /**
     * @return bool
     */
    public function isLogin()
    {
        return $this->_httpContext->getValue(CustomerContext::CONTEXT_AUTH);
    }

    public function getDevelop()
    {
        $current_product = $this->_registry->registry('current_product');
        $collection = $this->getResourceForum()->getDevelop($current_product->getId()); 
        return $collection;
    }

    public function getMediaUrl() 
    {
        return  $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
    }

    public function getCommentinForum($customer_id)
    {
        $cmt = $this->forumFactory->create()->getCollection()
                    ->addFieldToFilter('customer_id', $customer_id);
        return $cmt->count();
    }

    public function getCommentForums($forum_id)
    {
        $cmt = $this->commentForumFactory->create()->getCollection()
                    ->addFieldToFilter('forum_id', $forum_id);
        return $cmt;
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

    public function getDateFormat($date, $monthly = false)
    {
        $dateTime = new \DateTime($date, new DateTimeZone('UTC'));
        $dateTime->setTimezone(new DateTimeZone($this->getTimezone()));

        $dateType = $this->getCommunityConfig($monthly ? 'monthly_archive/date_type_monthly' : 'general/date_type');
        return $dateTime->format($dateType);
    }

    public function getCommunityConfig($code, $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(self::CONFIG_MODULE_PATH . $code, $storeId);
    }

    public function getTimezone()
    {
        return $this->getConfigValue('general/locale/timezone');
    }
    

    public function getAuthorByCustomerId($customer_id=null)
    { 
        $customer = $this->_customers->load($customer_id);
        return $customer;
    }

    public function getIdeaCollection()
    {
        $collection = $this->getCollection();
        if ($collection && $collection->getSize()) {
            $pager = $this->getLayout()->createBlock(Pager::class, 'community.forum.pager');
            $perPageValues = (string)$this->coreHelper->getConfigGeneral('pagination');
            $perPageValues = explode(',', $perPageValues);
            $perPageValues = array_combine($perPageValues, $perPageValues);
            $pager->setAvailableLimit($perPageValues)
                ->setCollection($collection);

            $this->setChild('pager', $pager);
        }
        return $collection;
    }

    public function getTopics($develop_id = null) 
    {
        $pager = $this->getLayout()->createBlock(Pager::class, 'community.forum.pager');
        $perPageValues = (string)$this->coreHelper->getConfigGeneral('pagination');
        $perPageValues = explode(',', $perPageValues);
        $perPageValues = array_combine($perPageValues, $perPageValues);
        $collection =  $this->forumFactory->create()->getForumDevelop($develop_id);
        $pager->setAvailableLimit($perPageValues)
                ->setCollection($collection);
            $this->setChild('pager', $pager);
        return $collection;
    }

    public function getPostByIdea($idea_id) {
        $collection =  $this->coreHelper->getTopics($idea_id)
                        ->setOrder('topic_id','DESC');
        if($collection) {
            return  $collection->getFirstItem();
        } else {
            return array();
        }

    }

    public function getNumberPostByIdea($idea_id) {
        $collection =  $this->coreHelper->getTopics($idea_id)
                        ->setOrder('topic_id','ASC');
        if($collection) {
            return  $collection->count();
        } else {
            return 0;
        }
    }


    /**
     * find /n in text
     *
     * @param $description
     *
     * @return string
     */
    public function maxShortDescription($description)
    {
        if (is_string($description)) {
            $html = '';
            foreach (explode("\n", trim($description)) as $value) {
                $html .= '<p>' . $value . '</p>';
            }
            return $html;
        }
        return $description;
    }

    /**
     * @return Collection
     */
    protected function getCollection()
    {
        try {
            return $this->helperData->getIdeaCollection(null, null, $this->store->getStore()->getId());
        } catch (Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }

        return null;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return array
     */
    protected function getBreadcrumbsData()
    {
        $label = __('Share Product Ideas');

        $data = [
            'label' => $label,
            'title' => $label
        ];

        if ($this->getRequest()->getFullActionName() !== 'community_idea_index') {
            $data['link'] = $this->_storeManager->getStore()->getBaseUrl().'community/idea';
        }

        return $data;
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    public function applySeoCode()
    {
        $this->pageConfig->getTitle()->set(join($this->getTitleSeparator(), array_reverse($this->getCommunityTitle(true))));

        $object = $this->getCommunityObject();

        $description = $object ? $object->getMetaDescription() : $this->helperData->getSeoConfig('meta_description');
        $this->pageConfig->setDescription($description);

        $keywords = $object ? $object->getMetaKeywords() : $this->helperData->getSeoConfig('meta_keywords');
        $this->pageConfig->setKeywords($keywords);

        $robots = $object ? $object->getMetaRobots() : $this->helperData->getSeoConfig('meta_robots');
        $this->pageConfig->setRobots($robots);

        if ($this->getRequest()->getFullActionName() === 'community_idea_view') {
            $this->pageConfig->addRemotePageAsset(
                $object->getUrl(),
                'canonical',
                ['attributes' => ['rel' => 'canonical']]
            );
        }
        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle($this->getCommunityTitle());
        }

        return $this;
    }

    /**
     * Retrieve HTML title value separator (with space)
     *
     * @return string
     */
    public function getTitleSeparator()
    {
        $separator = (string)$this->helperData->getConfigValue('catalog/seo/title_separator');

        return ' ' . $separator . ' ';
    }

    /**
     * @param bool $meta
     *
     * @return array|Phrase
     */
    public function getCommunityTitle($meta = false)
    {
        $pageTitle =  __('Share Product Ideas');
        if( $this->getRequest()->getParam('id') ) 
        {
            $idea = $this->ideaFactory->create()->load($this->getRequest()->getParam('id'));
            $pageTitle = $idea->getName();
        }
        if ($meta) {
            $title = $this->helperData->getSeoConfig('meta_title') ?: $pageTitle;

            return [$title];
        }

        return $pageTitle;
    }

    public function getIdeaUrl($id)
    {
        if($id >0) {
			
			$idea = $this->ideaFactory->create()->load($id);
            //echo "<pre>"; print_r($idea->getData()); die;
			if($idea->getData('url_key') !="") {
				return $this->getUrl('community/idea/'.$idea->getUrlKey());
			} else {
				return $this->getUrl('community/idea/view', ['id'=>$id]);
			}
		}
		return null;
    }
    public function getAuthorUrl($id=null,$idea_id=null)
    {
        if($id >0) {
			
			$author = $this->helperData->getAuthorById($id);
            //echo "<pre>"; print_r($idea->getData()); die;
			if($author->getData('url_key') !="") {
				return $this->getUrl('community/author/'.$author->getUrlKey());
			} else {
				return $this->getUrl('community/author/information', ['id'=>$id, 'idea_id'=>$idea_id]);
			}
		}
		return null;
    }

    public function getIdeaInfoByAuthor($author_id)
    {
        if($author_id)
        {
            $idea = $this->ideaFactory->create()->getCollection()
                ->addFieldToFilter('author_id',$author_id);
            if($idea->count())
            {
                return $idea->getLastItem();
            } else {
                return array();
            }

        }
    }
}
