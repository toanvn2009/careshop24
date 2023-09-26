<?php

namespace Careshop\CommunityIdea\Block\Idea;

use Careshop\CommunityIdea\Block\Frontend;
use Careshop\CommunityIdea\Helper\Data;


class ManageIdea extends Frontend
{
    /**
     * @return string
     */
    public function getCategoriesTree()
    {
        return Data::jsonEncode($this->categoryOptions->getCategoriesTree());
    }

    /**
     * @return string
     */
    public function getTopicTree()
    {
        return Data::jsonEncode($this->topicOptions->getTopicsCollection());
    }

    /**
     * @return string
     */
    public function getTagTree()
    {
        return Data::jsonEncode($this->tagOptions->getTagsCollection());
    }

    /**
     * @return bool
     */
    public function checkTheme()
    {
        return $this->themeProvider->getThemeById($this->helperData->getCurrentThemeId())
                ->getCode() === 'Smartwave/porto';
    }
}
