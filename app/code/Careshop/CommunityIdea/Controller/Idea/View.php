<?php

namespace Careshop\CommunityIdea\Controller\Idea;

use Exception;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Data\Customer;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Json\Helper\Data as JsonData;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Careshop\CommunityIdea\Helper\Data;
use Careshop\CommunityIdea\Helper\Data as HelperCommunity;
use Careshop\CommunityIdea\Model\Comment;
use Careshop\CommunityIdea\Model\CommentFactory;
use Careshop\CommunityIdea\Model\Config\Source\Comments\Status;
use Careshop\CommunityIdea\Model\Like;
use Careshop\CommunityIdea\Model\LikeFactory;
use Careshop\CommunityIdea\Model\IdeaFactory;
use Careshop\CommunityIdea\Model\TrafficFactory;

class View extends Action
{
    const COMMENT = 1;
    const LIKE    = 2;

    /**
     * @var TrafficFactory
     */
    protected $trafficFactory;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var HelperCommunity
     */
    protected $helperCommunity;

    /**
     * @var AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var CustomerUrl
     */
    protected $customerUrl;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var JsonData
     */
    protected $jsonHelper;

    /**
     * @var CommentFactory
     */
    protected $cmtFactory;

    /**
     * @var LikeFactory
     */
    protected $likeFactory;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var TimezoneInterface
     */
    protected $timeZone;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var IdeaFactory
     */
    protected $ideaFactory;

    /**
     * View constructor.
     *
     * @param Context $context
     * @param ForwardFactory $resultForwardFactory
     * @param StoreManagerInterface $storeManager
     * @param JsonData $jsonHelper
     * @param CommentFactory $commentFactory
     * @param LikeFactory $likeFactory
     * @param DateTime $dateTime
     * @param TimezoneInterface $timezone
     * @param HelperCommunity $helperCommunity
     * @param PageFactory $resultPageFactory
     * @param AccountManagementInterface $accountManagement
     * @param CustomerUrl $customerUrl
     * @param Session $customerSession
     * @param TrafficFactory $trafficFactory
     * @param IdeaFactory $ideaFactory
     */
    public function __construct(
        Context $context,
        ForwardFactory $resultForwardFactory,
        StoreManagerInterface $storeManager,
        JsonData $jsonHelper,
        CommentFactory $commentFactory,
        LikeFactory $likeFactory,
        DateTime $dateTime,
        TimezoneInterface $timezone,
        HelperCommunity $helperCommunity,
        PageFactory $resultPageFactory,
        AccountManagementInterface $accountManagement,
        CustomerUrl $customerUrl,
        Session $customerSession,
        TrafficFactory $trafficFactory,
        IdeaFactory $ideaFactory
    ) {
        $this->storeManager         = $storeManager;
        $this->helperCommunity           = $helperCommunity;
        $this->resultPageFactory    = $resultPageFactory;
        $this->accountManagement    = $accountManagement;
        $this->customerUrl          = $customerUrl;
        $this->session              = $customerSession;
        $this->timeZone             = $timezone;
        $this->trafficFactory       = $trafficFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->jsonHelper           = $jsonHelper;
        $this->cmtFactory           = $commentFactory;
        $this->likeFactory          = $likeFactory;
        $this->dateTime             = $dateTime;
        $this->ideaFactory          = $ideaFactory;

        parent::__construct($context);
    }

       /**
     * @return $this|ResponseInterface|ResultInterface|Page
     * @throws Exception
     */
    public function execute()
    {

        $page = $this->resultPageFactory->create();
        $page->getConfig()->setPageLayout('1column');
        return $page;


    }


    public function getIdea() {
        if(!$this->getRequest()->getParam('id')) return array();
        $id   = $this->getRequest()->getParam('id');
        $idea = $this->helperCommunity->getFactoryByType(Data::TYPE_IDEA)->create()->load($id);
        return $idea ; 
    }


   
}
