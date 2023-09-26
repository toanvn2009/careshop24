<?php

namespace Careshop\CommunityIdea\Model;

class Layer extends \Magento\Catalog\Model\Layer
{
    public function getProductCollection()
    {
        $collection = parent::getProductCollection();
        $collection = $collection->addFieldToFilter('attribute_set_id', 15);
                      //->addFieldToFilter('product_community',5556);
        
        return $collection;
    }
}