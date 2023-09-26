<?php

namespace Careshop\CommunityIdea\Controller\Idea;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Careshop\CommunityIdea\Model\IdeaFactory;
use Careshop\CommunityIdea\Model\AuthorFactory;
use Careshop\CommunityIdea\Model\LikeFactory;
use Careshop\CommunityIdea\Helper\Data as HelperData;
class CountView extends Action
{
    /**
     * Idea Factory
     *
     * @var IdeaFactory
     */
    protected $ideaFactory;

    protected $_pageFactory;

    protected $storeManager;
	
	protected $authorFactory;

    protected $likeFactory;
	/**
     * @var HelperData
     */
    public $helperData;
    private $json;
    private $resultJsonFactory;

    public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        IdeaFactory $ideaFactory,
		AuthorFactory $authorFactory,
        LikeFactory $likeFactory,
		HelperData $helperData
        )
	{
		$this->_pageFactory = $pageFactory;
        $this->ideaFactory = $ideaFactory;
        $this->storeManager = $storeManager;
		$this->authorFactory = $authorFactory;
		$this->helperData    = $helperData;
        $this->likeFactory = $likeFactory;
        $this->json = $json;
        $this->resultJsonFactory = $resultJsonFactory;
		return parent::__construct($context);
	}

    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface|Page
     */
    public function execute()
    {   
        $data = $this->getRequest()->getPost();
        $params = $this->getRequest()->getParams(); 
        if(  $data['idea_id']) {
            $idea = $this->ideaFactory->create()->load($data['idea_id']);
            $view = $idea->getViews();
            $view += 1; 
            $idea ->setViews($view);
            $idea->save();
        }
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($data);
    }
}
