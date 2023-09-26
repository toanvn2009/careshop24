<?php

namespace Careshop\CommunityIdea\Controller\Topic;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Careshop\CommunityIdea\Model\IdeaFactory;
use Careshop\CommunityIdea\Model\AuthorFactory;
use Careshop\CommunityIdea\Model\LikeFactory;
use Careshop\CommunityIdea\Model\IdeaLikeFactory;
use Careshop\CommunityIdea\Helper\Data as HelperData;
class Like extends Action
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
    protected $ideaLikeFactory;
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
        IdeaLikeFactory $ideaLikeFactory,
		HelperData $helperData
        )
	{
		$this->_pageFactory = $pageFactory;
        $this->ideaFactory = $ideaFactory;
        $this->storeManager = $storeManager;
		$this->authorFactory = $authorFactory;
		$this->helperData    = $helperData;
        $this->likeFactory = $likeFactory;
        $this->ideaLikeFactory = $ideaLikeFactory;
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
        $response = $this->getRequest()->getParams();
        $customer_current_id = $this->helperData->getCurrentUser();
        $likeData = array();
        if($data['comment_id']) {
            $like = $this->likeFactory->create();
            $comment_id = $data['comment_id'];
            $entity_id = $data['entity_id'];
            $likeData = array(
                'comment_id' => $comment_id,
                'entity_id' => $entity_id,
                'customer_id' => $customer_current_id
            );
            $like->setData($likeData);
            $like->save();
            $likeData['like_comment'] = $this->getCommentLikes($comment_id);
        } elseif($data['topic_id']) {

            $idea_like = $this->ideaLikeFactory->create();
            $idea_id = $data['idea_id'];
            $topic_id = $data['topic_id'];
            $entity_id = $customer_current_id;
            $likeData = array(
                'idea_id' => $idea_id,
                'topic_id'=>$topic_id,
                'entity_id' => $entity_id
            );
            $idea_like->setData($likeData);
            $idea_like->save();
            $likeData['like_idea'] =  $this->getIdeaLikeFromTopic($topic_id);
        }
       
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($likeData);
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

    /**
     * @param $cmtId
     *
     * @return int|string
     */
    public function getCommentLikes($cmtId)
    {
        $likes = $this->likeFactory->create()
            ->getCollection()
            ->addFieldToFilter('comment_id', $cmtId)
            ->getSize();

        return $likes ?: '';
    }

}
