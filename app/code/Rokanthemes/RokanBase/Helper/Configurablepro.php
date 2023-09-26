<?php

namespace Rokanthemes\RokanBase\Helper;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class Configurablepro extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    protected $configurableProduct;
    
    public function __construct(
        Configurable $configurableProduct,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->configurableProduct = $configurableProduct;
        parent::__construct($context);
    }

    public function getPriceRange($product)
    {
        if($product->getTypeId() != \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE){
            return [];
        }
        $childProductPrice = [];
        $childProducts = $this->configurableProduct->getUsedProducts($product);
        foreach($childProducts as $child) {
            $price = number_format($child->getPrice(), 2, '.', '');
            $finalPrice = number_format($child->getFinalPrice(), 2, '.', '');
            if($price == $finalPrice) {
                $childProductPrice[] = $price;
            }
            elseif($finalPrice < $price) {
                $childProductPrice[] = $finalPrice;
            }
        }
        $max = max($childProductPrice);
        $min = min($childProductPrice);
        return ['min' => $min, 'max' => $max];
    }

    public function getRelatedProduct($product_id)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $sql = "SELECT catalog_product_link.linked_product_id, catalog_product_link_attribute_int.value as postions FROM catalog_product_link LEFT JOIN catalog_product_link_attribute_int ON catalog_product_link.link_id = catalog_product_link_attribute_int.link_id WHERE catalog_product_link.product_id = ".$product_id." AND catalog_product_link_attribute_int.product_link_attribute_id = 1 ORDER BY catalog_product_link_attribute_int.value ASC";
        $result = $connection->fetchAll($sql);
        return $result;
    }
}
