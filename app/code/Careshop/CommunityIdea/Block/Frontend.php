<?php

namespace Careshop\CommunityIdea\Block;

use Exception;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Url;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Careshop\CommunityIdea\Block\Adminhtml\Idea\Edit\Tab\Renderer\Category as CategoryOptions;
use Careshop\CommunityIdea\Block\Adminhtml\Idea\Edit\Tab\Renderer\Tag as TagOptions;
use Careshop\CommunityIdea\Block\Adminhtml\Idea\Edit\Tab\Renderer\Topic as TopicOptions;
use Careshop\CommunityIdea\Helper\Data as HelperData;
use Careshop\CommunityIdea\Helper\Image;
use Careshop\CommunityIdea\Model\CategoryFactory;
use Careshop\CommunityIdea\Model\CommentFactory;
use Careshop\CommunityIdea\Model\Config\Source\AuthorStatus;
use Careshop\CommunityIdea\Model\LikeFactory;
use Careshop\CommunityIdea\Model\Idea;
use Careshop\CommunityIdea\Model\IdeaFactory;
use Careshop\CommunityIdea\Model\IdeaLikeFactory;
use Careshop\CommunityCustomer\Model\ProfileFactory;

class Frontend extends Template
{
    /**
     * @var FilterProvider
     */
    public $filterProvider;

    /**
     * @var HelperData
     */
    public $helperData;

    /**
     * @var StoreManagerInterface
     */
    public $store;

    /**
     * @var CommentFactory
     */
    public $cmtFactory;

    /**
     * @var LikeFactory
     */
    public $likeFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    public $customerRepository;

    /**
     * @var
     */
    public $commentTree;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var DateTime
     */
    public $dateTime;

    /**
     * @var IdeaFactory
     */
    protected $ideaFactory;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var Url
     */
    protected $customerUrl;

    /**
     * @var CategoryOptions
     */
    protected $categoryOptions;

    /**
     * @var TopicOptions
     */
    protected $topicOptions;

    /**
     * @var TagOptions
     */
    protected $tagOptions;

    /**
     * @var IdeaLikeFactory
     */
    protected $ideaLikeFactory;

    /**
     * @var AuthorStatus
     */
    protected $authorStatusType;

    /**
     * @var ThemeProviderInterface
     */
    protected $themeProvider;

    /**
     * @var EncryptorInterface
     */
    public $enc;

    protected $profileFactory;

    protected $_visitor;

    protected $session;

    /**
     * Frontend constructor.
     *
     * @param Context $context
     * @param FilterProvider $filterProvider
     * @param CommentFactory $commentFactory
     * @param LikeFactory $likeFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param Registry $coreRegistry
     * @param HelperData $helperData
     * @param Url $customerUrl
     * @param CategoryFactory $categoryFactory
     * @param IdeaFactory $ideaFactory
     * @param DateTime $dateTime
     * @param IdeaLikeFactory $ideaLikeFactory
     * @param CategoryOptions $category
     * @param TopicOptions $topic
     * @param TagOptions $tag
     * @param ThemeProviderInterface $themeProvider
     * @param EncryptorInterface $enc
     * @param AuthorStatus $authorStatus
     * @param array $data
     */
    public function __construct(
        Context $context,
        FilterProvider $filterProvider,
        CommentFactory $commentFactory,
        LikeFactory $likeFactory,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\CustomerFactory $customer,
        Registry $coreRegistry,
        HelperData $helperData,
        Url $customerUrl,
        CategoryFactory $categoryFactory,
        IdeaFactory $ideaFactory,
        DateTime $dateTime,
        IdeaLikeFactory $ideaLikeFactory,
        CategoryOptions $category,
        TopicOptions $topic,
        TagOptions $tag,
        ThemeProviderInterface $themeProvider,
        EncryptorInterface $enc,
        AuthorStatus $authorStatus,
        ProfileFactory $profileFactory,
        \Magento\Customer\Model\Visitor $visitor, 
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Session\SessionManagerInterface $session,
        array $data = []
    ) {
        $this->filterProvider     = $filterProvider;
        $this->cmtFactory         = $commentFactory;
        $this->likeFactory        = $likeFactory;
        $this->customerRepository = $customerRepository;
        $this->helperData         = $helperData;
        $this->coreRegistry       = $coreRegistry;
        $this->dateTime           = $dateTime;
        $this->categoryFactory    = $categoryFactory;
        $this->ideaFactory        = $ideaFactory;
        $this->customerUrl        = $customerUrl;
        $this->ideaLikeFactory    = $ideaLikeFactory;
        $this->categoryOptions    = $category;
        $this->topicOptions       = $topic;
        $this->customer = $customer;
        $this->tagOptions         = $tag;
        $this->authorStatusType   = $authorStatus;
        $this->themeProvider      = $themeProvider;
        $this->store              = $context->getStoreManager();
        $this->enc                = $enc;
        $this->profileFactory  = $profileFactory;
        $this->_visitor = $visitor;
        $this->_resource = $resource;
        $this->session = $session;
        parent::__construct($context, $data);
    }

    /**
     * @return HelperData
     */
    public function getCommunityHelper()
    {
        return $this->helperData;
    }

    /**
     * @return bool
     */
    public function isCommunityEnabled()
    {
        return $this->helperData->isEnabled();
    }

    /**
     * @param string $content
     *
     * @return string
     */
    public function getPageFilter($content)
    {
        try {
            return $this->filterProvider->getPageFilter()->filter((string) $content);
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * @param string $image
     * @param string $type
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getImageUrl($image, $type = Image::TEMPLATE_MEDIA_TYPE_IDEA)
    {
        $imageHelper = $this->helperData->getImageHelper();
        $imageFile   = $imageHelper->getMediaPath($image, $type);

        return $this->helperData->getImageHelper()->getMediaUrl($imageFile);
    }

    /**
     * @param string|Object $urlKey
     * @param null $type
     *
     * @return string
     */
    public function getRssUrl($urlKey, $type = null)
    {
        if (is_object($urlKey)) {
            $urlKey = $urlKey->getUrlKey();
        }

        $urlKey = ($type ? $type . '/' : '') . $urlKey;
        $url    = $this->helperData->getUrl($this->helperData->getRoute() . '/' . $urlKey);

        return rtrim($url, '/') . '.xml';
    }

    /**
     * @param Idea $idea
     *
     * @return Phrase|string
     */
    public function getIdeaInfo($idea)
    {
        try {
            $likeCollection = $this->ideaLikeFactory->create()->getCollection();
            $couldLike      = $likeCollection->addFieldToFilter('idea_id', $idea->getId())
                ->addFieldToFilter('action', '1')->count();
            $html           = __(
                '<i class="cscm-community-icon cscm-community-calendar-times"></i> %1',
                $this->getDateFormat($idea->getPublishDate())
            );

            if ($categoryIdea = $this->getIdeaCategoryHtml($idea)) {
                $html .= __('| Posted in %1', $categoryIdea);
            }

            $author = $this->helperData->getAuthorByIdea($idea);
            if ($author && $author->getName() && $this->helperData->showAuthorInfo()) {
                $aTag = '<a class="cscm-info" href="' . $author->getUrl() . '">'
                    . $this->escapeHtml($author->getName()) . '</a>';
                $html .= __('| <i class="cscm-community-icon cscm-community-user"></i> %1', $aTag);
            }

            if ($this->getCommentinIdea($idea)) {
                $html .= __(
                    '| <i class="cscm-community-icon cscm-community-comments" aria-hidden="true"></i> %1',
                    $this->getCommentinIdea($idea)
                );
            }

            if ($idea->getViewTraffic()) {
                $html .= __(
                    '| <i class="cscm-community-icon cscm-community-traffic" aria-hidden="true"></i> %1',
                    $idea->getViewTraffic()
                );
            }

            if ($couldLike > 0) {
                $html .= __('| <i class="cscm-community-icon cscm-community-thumbs-up" aria-hidden="true"></i> %1', $couldLike);
            }
        } catch (Exception $e) {
            $html = '';
        }

        return $html;
    }

    /**
     * @param Idea $idea
     *
     * @return int
     */
    public function getCommentinIdea($idea)
    {
        $cmt = $this->cmtFactory->create()->getCollection()->addFieldToFilter('idea_id', $idea->getId());

        return $cmt->count();
    }

    /**
     * @param $customer_id
     *
     * @return int
     */
    public function getCommentinCustomer($customer_id)
    {
        $cmt = $this->cmtFactory->create()->getCollection()
                    ->addFieldToFilter('entity_id', $customer_id);

        return $cmt->count();
    }
    
    public function getCommentinTopic($customer_id)
    {

        return $this->helperData->getCommentinTopic($customer_id);
    }


        /**
     * @param Idea $idea
     *
     * @return int
     */
    public function getCommentByIdea($idea,$topic)
    {
        $cmt = $this->cmtFactory->create()->getCollection()
            ->addFieldToFilter('idea_id', $idea->getId())
            ->addFieldToFilter('topic_id', $topic->getTopicId());

        return $cmt;
    }


    public function getAuthorByCustomerId($customer_id=null)
    { 
      return $this->helperData-> getAuthorByCustomerId($customer_id);
    }

    public function getAllCommentLikes($idea_id = null)
    {
        return $this->cmtFactory->create()-> getAllCommentLikeIdeas($idea_id)->count();
    }

    /**
     * Get list category html of post
     *
     * @param Idea $idea
     *
     * @return string|null
     */
    public function getIdeaCategoryHtml($idea)
    {
        $categoryHtml = [];

        try {
            if (!$idea->getCategoryIds()) {
                return null;
            }

            $categories = $this->helperData->getCategoryCollection($idea->getCategoryIds());
            foreach ($categories as $_cat) {
                $categoryHtml[] = '<a class="cscm-info" href="'
                    . $this->helperData->getCommunityUrl(
                        $_cat,
                        HelperData::TYPE_CATEGORY
                    )
                    . '">' . $_cat->getName() . '</a>';
            }
        } catch (Exception $e) {
            return null;
        }

        return implode(', ', $categoryHtml);
    }

    /**
     * @param string $date
     * @param bool $monthly
     *
     * @return false|string|null
     */
    public function getDateFormat($date, $monthly = false)
    {
        try {
            $date = $this->helperData->getDateFormat($date, $monthly);
        } catch (Exception $e) {
            $date = null;
        }

        return $date;
    }

    /**
     * @param string $image
     * @param null $size
     * @param string $type
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function resizeImage($image, $size = null, $type = Image::TEMPLATE_MEDIA_TYPE_IDEA)
    {
        if (!$image) {
            return $this->getDefaultImageUrl();
        }

        return $this->helperData->getImageHelper()->resizeImage($image, $size, $type);
    }

    /**
     * get default image url
     */
    public function getDefaultImageUrl()
    {
        return $this->getViewFileUrl('Careshop_CommunityIdea::media/images/mageplaza-logo-default.png');
    }

    /**
     * @return string
     */
    public function getDefaultAuthorImage()
    {
        return $this->getViewFileUrl('Careshop_CommunityIdea::media/images/no-artist-image.jpg');
    }

    public function isLogin() {
        return $this->helperData -> isLogin();
    }

    public function getMediaUrl() 
    {
        return  $this->helperData->getMediaUrl();
    }

    /**
     * @param $topic_id
     *
     * @return int|string
     */
    public function getIdeaLikeFromTopic($topic_id)
    {
        $likes = $this->ideaLikeFactory->create()
            ->getCollection()
            ->addFieldToFilter('topic_id', $topic_id)
            ->getSize();

        return $likes ?: '';
    }

    public function getAllIdeaLikes($idea_id = null)
    {
        $likes = $this->ideaLikeFactory->create()
            ->getCollection()
            ->addFieldToFilter('idea_id', $idea_id)
            ->getSize();

        return $likes ?: '';
    }

    public function getAllLikesFromEntityID($entity_id = null)
    {
        $likes = $this->ideaLikeFactory->create()
            ->getCollection()
            ->addFieldToFilter('entity_id', $entity_id)
            ->getSize();

        return $likes ?: 0;
    }
    /**
     * @param $entityId
     *
     * @return int|string
     */
    public function getUserHasLikedComment($entiy_id)
    {  
        $likes = $this->likeFactory->create()
            ->getCollection()
            ->addFieldToFilter('entity_id', $entiy_id)
            ->getSize();

        return $likes ?: 0;
    }


    public function getAllMembers()
    {
        $profileCollection = $this->profileFactory->create()
        ->getCollection();
        if($profileCollection->count())
        {
            return $profileCollection;
        }else {
            return array();
        }
    }

    public function getAllAuthor() 
    {
        return $this->helperData->getAllAuthor();
    }

    public function getUserData() 
    {
        return $this->helperData->getUserData();
    }

    public function getCustomerInfo($customer_id) {
        if($customer_id) {
            $customer = $this->customerRepository->getById($customer_id);
            return $customer;
        }
        return null;
    }

    public function getVisitors($customer_id)
    {
        $visitors = $this->_visitor->getCollection()
            ->addFieldToFilter('customer_id',$customer_id);
        if($visitors->count()){
            $visitors = $visitors->getLastItem();
            return $visitors; 
        }
        return '';
    }

    public function convertTimeFromDate($startdate, $enddate)
    {
       // $hours_current = $date_current - $date ;
       // $hours = (int)($hours_current/60/60);
       
        $date1 =  new \DateTime($startdate);
        $date2 =  new \DateTime($enddate);
        $diff = $date2->diff($date1);
        $hours = $diff->h;
        $minutes = $diff->i;
        $second = $diff->s;

        $hours = $hours + ($diff->days*24);
        $current_date =  $hours;

        $current_date_1 = $current_date. __(' Hours ago');
        if($current_date == 0) {
          $current_date_1 = $minutes. __(' Minutes ago');
          if($current_date_1 == 0) {
            $current_date_1 = $second. __(' Seconds ago');
          }
        }
        if($current_date > 24){
           $current_date_1 = round($current_date/24). __(' day ago');
        }
        return $current_date_1;
    }

    public function getVistor(){
        $visitor = $this->session->getVisitorData();
    }


}
