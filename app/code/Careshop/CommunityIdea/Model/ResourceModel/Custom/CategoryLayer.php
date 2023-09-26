<?php

namespace Careshop\CommunityIdea\Model\ResourceModel\Custom;

class CategoryLayer extends \Magento\Catalog\Model\ResourceModel\Category
{
    public function getProductCount($category)
    {  die('aa');
        $collection = $category->getProductCollection();
        //add custom filter
        //$collection->addAttributeToFilter('deal_status', ['eq' => '1']);
		$idArray = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15];
		if (!empty($idArray)) {
            $collection->addAttributeToFilter('entity_id', ["in"=>$idArray]);
        }
        return intval($collection->count());
    }
}