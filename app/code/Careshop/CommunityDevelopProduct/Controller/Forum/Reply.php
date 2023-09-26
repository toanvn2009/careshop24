<?php

namespace Careshop\CommunityDevelopProduct\Controller\Forum;

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
use Careshop\CommunityDevelopProduct\Model\ForumFactory;
use Careshop\CommunityDevelopProduct\Model\DevelopFactory;

class Reply extends Action
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
     * Topic Factory
     *
     * @var TopicFactory
     */
    public $topicFactory;


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
     * @param TopicFactory $topicFactory
     */
    public function __construct(
        Context $context,
        ForwardFactory $resultForwardFactory,
        StoreManagerInterface $storeManager,
        JsonData $jsonHelper,
        DateTime $dateTime,
        TimezoneInterface $timezone,
        PageFactory $resultPageFactory,
        AccountManagementInterface $accountManagement,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        ForumFactory $forumFactory,
        DevelopFactory $developFactory,
        CustomerUrl $customerUrl,
        Session $customerSession
    ) {
        $this->storeManager         = $storeManager;
        $this->resultPageFactory    = $resultPageFactory;
        $this->accountManagement    = $accountManagement;
        $this->customerUrl          = $customerUrl;
        $this->session              = $customerSession;
        $this->timeZone             = $timezone;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->forumFactory = $forumFactory;
        $this->developFactory = $developFactory;
        $this->_productloader = $_productloader;
        $this->jsonHelper           = $jsonHelper;
        $this->dateTime             = $dateTime;
        parent::__construct($context);
    }

       /**
     * @return $this|ResponseInterface|ResultInterface|Page
     * @throws Exception
     */
    public function execute()
    {
     
        $data = $this->getRequest()->getPost();
        $resultRedirect = $this->resultRedirectFactory->create();
        if(isset($data['message']) && isset($data['name'])) {  
            $id = $this->getRequest()->getParam('id');
            if(trim($data['message']) == "" || trim($data['name']) == "") { 
                $this->messageManager->addErrorMessage(__('You need enter the content'));
                $resultRedirect->setPath('communitydevelop/*/reply',array('id'=>$id));
                return $resultRedirect;
            }
            $_develop = $this->getDevelop();
            $_product = $this->getProduct();
            $customer_id = $_develop->getCustomerId();
            $dataForum = array(
                'develop_id' => $id,
                'customer_id' => $customer_id,
                'product_id' => $_product->getId(),
                'name' => ($data['name']) ? $data['name'] : '',
                'description' => ($data['message']) ? $data['message'] : ''
            );
            $forum = $this->forumFactory->create();
            $forum->addData($dataForum); 
            $forum->Save();
            $resultRedirect->setPath('communitydevelop/*/success',array('id'=>$id, 'forum_id' => $forum->getId()));
            return $resultRedirect;

        } 
        $page = $this->resultPageFactory->create();
        $page->getConfig()->setPageLayout('1column');
        $page->getConfig()->getTitle()->prepend(__('Reply Forum'));
        return $page;
    }

    public function getDevelop()
    {
        $develop = $this->developFactory->create();
        $id = $this->getRequest()->getParam('id');
        $develop->load($id);
        return $develop;
    }

    /**
     * @return bool
     */
    public function getProduct()
    {
        $develop = $this->getDevelop();
        $_product = $this->_productloader->create()->load($develop->getProductId());
        return $_product;
    }

}
