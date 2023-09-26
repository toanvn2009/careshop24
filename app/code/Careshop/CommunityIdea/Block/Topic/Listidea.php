<?php

namespace Careshop\CommunityIdea\Block\Topic;

use Careshop\CommunityIdea\Helper\Data;
use Careshop\CommunityIdea\Model\ResourceModel\Idea\Collection;
use Careshop\CommunityIdea\Model\TopicFactory;

/**
 * Class Listidea
 * @package Careshop\CommunityIdea\Block\Topic
 */
class Listidea extends \Careshop\CommunityIdea\Block\Listidea
{
    /**
     * @var TopicFactory
     */
    protected $_topic;

    /**
     * Override this function to apply collection for each type
     *
     * @return Collection
     */
    protected function getCollection()
    {
        if ($topic = $this->getCommunityObject()) {
            return $this->helperData->getIdeaCollection(Data::TYPE_TOPIC, $topic->getId());
        }

        return null;
    }

    /**
     * @return mixed
     */
    protected function getCommunityObject()
    {
        if (!$this->_topic) {
            $id = $this->getRequest()->getParam('id');

            if ($id) {
                $topic = $this->helperData->getObjectByParam($id, null, Data::TYPE_TOPIC);
                if ($topic && $topic->getId()) {
                    $this->_topic = $topic;
                }
            }
        }

        return $this->_topic;
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $topic = $this->getCommunityObject();
            if ($topic) {
                $breadcrumbs->addCrumb($topic->getUrlKey(), [
                    'label' => __('Topic'),
                    'title' => __('Topic')
                ]);
            }
        }
    }

    /**
     * @param bool $meta
     *
     * @return array
     */
    public function getCommunityTitle($meta = false)
    {
        $communityTitle = parent::getCommunityTitle($meta);
        $topic = $this->getCommunityObject();
        if (!$topic) {
            return $communityTitle;
        }

        if ($meta) {
            if ($topic->getMetaTitle()) {
                array_push($communityTitle, $topic->getMetaTitle());
            } else {
                array_push($communityTitle, ucfirst($topic->getName()));
            }

            return $communityTitle;
        }

        return ucfirst($topic->getName());
    }
}
