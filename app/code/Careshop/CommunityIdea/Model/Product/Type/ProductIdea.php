<?php
namespace Careshop\CommunityIdea\Model\Product\Type;

class ProductIdea extends \Magento\Catalog\Model\Product\Type\AbstractType
{
    const TYPE_CODE= 'ideas';

    public function save($product)
    {
        parent::save($product);
        //  your additional saving logic
        return $this;
    }

    public function hasOptions($product)
    {
        return true;
    }

    public function deleteTypeSpecificData(\Magento\Catalog\Model\Product $product)
    {

    }
}