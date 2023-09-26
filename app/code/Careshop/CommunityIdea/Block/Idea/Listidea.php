<?php

namespace Careshop\CommunityIdea\Block\Idea;

class Listidea extends \Careshop\CommunityIdea\Block\Listidea
{
    public function getAuthorByIdea($idea, $modify=false)
    {
        return $this->helperData->getAuthorByIdea($idea,$modify);
    }

    public function getRoute($store = null)
    {
        return $this->helperData->getRoute($store);
    }
    
    public function getCategoriesTree() {
        return $this->helperData->getCategoriesTree();
    }

}
