<?php

namespace Careshop\CommunityIdea\Controller\Topic;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Careshop\CommunityIdea\Model\IdeaFactory;
use Careshop\CommunityIdea\Model\AuthorFactory;
use Careshop\CommunityIdea\Model\CommentFactory;
use Careshop\CommunityIdea\Helper\Data as HelperData;
class Comment extends Action
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

    protected $commentFactory;
	/**
     * @var HelperData
     */
    public $helperData;


    public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        IdeaFactory $ideaFactory,
		AuthorFactory $authorFactory,
        CommentFactory $commentFactory,
		HelperData $helperData
        )
	{
		$this->_pageFactory = $pageFactory;
        $this->ideaFactory = $ideaFactory;
        $this->storeManager = $storeManager;
		$this->authorFactory = $authorFactory;
		$this->helperData    = $helperData;
        $this->commentFactory = $commentFactory;
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
        $comment = $this->commentFactory->create();
        $customer_current_id = $this->helperData->getCurrentUser();
        $commentData = array(
            'idea_id' => ($data['idea_id']) ? $data['idea_id'] : '',
            'topic_id' => ($data['topic_id']) ? $data['topic_id'] : '',
            'entity_id' => ($data['customer_id']) ? $data['customer_id'] : '',
            'reply_id' => ($customer_current_id ) ? $customer_current_id  : '',
            'status' => 1, 
            'content' => ($data['message_comment']) ? $data['message_comment'] : ''
        );
        $comment->setData($commentData);
        $comment->save();

        $resultRedirect = $this->resultRedirectFactory->create();
        $idea = $this->ideaFactory->create()->load($data['idea_id']);
        if($idea->getUrlKey()){
            $resultRedirect->setPath('community/idea/'.$idea->getUrlKey());
        }else {
            $resultRedirect->setPath('community/idea/view/id/'.$data['idea_id']);
        }
		$this->messageManager->addSuccessMessage(__('You Comment Succesfully!'));

        return $resultRedirect;
    }

}
