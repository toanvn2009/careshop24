<?php

namespace Careshop\CommunityIdea\Block\Idea;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Messages;
use Careshop\CommunityIdea\Helper\Data;
use Careshop\CommunityIdea\Model\Idea;
use Careshop\CommunityIdea\Model\IdeaLike;
use Careshop\CommunityIdea\Model\CommentFactory;

class View extends \Careshop\CommunityIdea\Block\Listidea
{

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();
        $idea = $this->ideaFactory->create();
        $id = $this->getRequest()->getParam('id');
        if($id) {
            $idea->load($id);
        }
        $this->setIdea($idea);
    }

    /**
     * @return bool
     */
    public function getRelatedMode()
    {
        return (int)$this->helperData->getConfigGeneral('related_mode') === 1 ? true : false;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getDecrypt($value)
    {
        return $this->enc->decrypt($value);
    }

    /**
     * @return mixed
     */
    protected function getCommunityObject()
    {
        return $this->getIdea();
    }

    /**
     * check customer is logged in or not
     */
    public function isLoggedIn()
    {
        return $this->helperData->isLogin();
    }

    /**
     * @return string
     */
    public function checkRss()
    {
        return $this->helperData->getCommunityUrl('post/rss');
    }

    /**
     * @param $topic
     *
     * @return string
     */
    public function getTopicUrl($topic)
    {
        return $this->helperData->getCommunityUrl($topic, Data::TYPE_TOPIC);
    }


    public function getTopicById($topic_id=null) 
    {
        return $this->helperData->getTopicById($topic_id);
    }

    public function getIdeaById($idea_id=null)
    {
        $idea = $this->helperData->getFactoryByType(Data::TYPE_IDEA)->create()->load($idea_id);
        return $idea;
    }

    public function getCustomerIdByAuthor($author_id = null)
    {
        $author  = $this->helperData->getAuthorById($author_id);
        return $author->getCustomerId();
    }

    public function getAuthor($author_id = null)
    {
        $author  = $this->helperData->getAuthorById($author_id);
        return $author;
    }

    public function getCustomerProfile($customer_id)
    {
        $profile  = $this->helperData->getCustomerProfile($customer_id);
        return $profile;
    }

    public function getMediaUrl(){
        return $this->helperData->getMediaUrl();
    }

    /**
     * @return mixed|string
     */
    public function getPubId()
    {
        return $this->helperData->getCommunityConfig('share/pubid_id') === 'ra-5983d393d9a9b2c9' ?
            $this->helperData->getCommunityConfig('share/pubid_id') :
            $this->getDecrypt($this->helperData->getCommunityConfig('share/pubid_id'));
    }

    /**
     * @param $tag
     *
     * @return string
     */
    public function getTagUrl($tag)
    {
        return $this->helperData->getCommunityUrl($tag, Data::TYPE_TAG);
    }

    /**
     * @param $category
     *
     * @return string
     */
    public function getCategoryUrl($category)
    {
        return $this->helperData->getCommunityUrl($category, Data::TYPE_CATEGORY);
    }

    /**
     * @param $code
     *
     * @return mixed
     */
    public function helperComment($code)
    {
        return $this->helperData->getCommunityConfig('comment/' . $code);
    }

    /**
     * get comments tree html
     *
     * @return mixed
     */
    public function getCommentsHtml()
    {
        return $this->commentTree;
    }

    /**
     * @param $userId
     *
     * @return CustomerInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getUserComment($userId)
    {
        return $this->customerRepository->getById($userId);
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

    /**
     * @param $ideaId
     *
     * @return array
     */
    public function getIdeaComments($ideaId)
    {
        $result = [];
        $comments = $this->cmtFactory->create()->getCollection()
            ->addFieldToFilter('main_table.idea_id', $ideaId);
        foreach ($comments as $comment) {
            $result[] = $comment->getData();
        }

        return $result;
    }

    /**
     * @param $ideaId
     * @param $action
     *
     * @return int
     */
    public function getIdeaLike($ideaId, $action)
    {
        /** @var IdeaLike $ideaLike */
        $ideaLike = $this->ideaLikeFactory->create();

        return $ideaLike->getCollection()->addFieldToFilter('idea_id', $ideaId)
            ->addFieldToFilter('action', $action)->count();
    }

    /**
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->customerUrl->getLoginUrl();
    }

    /**
     * @return string
     */
    public function getRegisterUrl()
    {
        return $this->customerUrl->getRegisterUrl();
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $idea = $this->getIdea();
            $breadcrumbs->addCrumb($idea->getUrlKey(), [
                'label' => $idea->getName(),
                'title' => $idea->getName()
            ]);
        }
    }

    public function getTopic()
    {
        $idea = $this->getIdea();
        $idea_id = $idea->getId();
        $parent = $this->getRequest()->getParam('parent');
        $tableName = $this->_resource->getTableName('community_topic');
        $connection = $this->_resource->getConnection();
        $sql = "SELECT * FROM $tableName WHERE idea_id = $idea_id AND identifier = '".$parent."'";
        $topic = $connection->fetchRow($sql);
        if ($topic) {
            $topic = $this->helperData->getTopicById($topic['topic_id']);
            return $topic;
        }
        return false;
    }

    public function getTopicParentById($topic_id = null)
    {
        return $this->helperData->getTopicById($topic_id); 
    }
}
