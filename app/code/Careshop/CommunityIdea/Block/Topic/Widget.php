<?php

namespace Careshop\CommunityIdea\Block\Topic;

use Exception;
use Careshop\CommunityIdea\Model\ResourceModel\Author\Collection as AuthorCollection;
use Careshop\CommunityIdea\Model\ResourceModel\Category\Collection as CategoryCollection;
use Careshop\CommunityIdea\Model\ResourceModel\Idea\Collection;
use Careshop\CommunityIdea\Model\ResourceModel\Tag\Collection as TagCollection;
use Careshop\CommunityIdea\Model\ResourceModel\Topic\Collection as TopicCollection;
use Careshop\CommunityIdea\Block\Frontend;
use Careshop\CommunityIdea\Helper\Data;
use Careshop\CommunityIdea\Model\Topic;

/**
 * Class Widget
 * @package Careshop\CommunityIdea\Block\Topic
 */
class Widget extends Frontend
{
    /**
     * @return AuthorCollection|CategoryCollection|Collection|TagCollection|TopicCollection|null
     */
    public function getTopicList()
    {
        try {
            return $this->helperData->getObjectList(Data::TYPE_TOPIC);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param Topic $topic
     *
     * @return string
     */
    public function getTopicUrl($topic)
    {
        return $this->helperData->getCommunityUrl($topic, Data::TYPE_TOPIC);
    }
}
