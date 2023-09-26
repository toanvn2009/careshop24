<?php

namespace Careshop\CommunityIdea\Ui\Component\Form\Categories;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;
use Careshop\CommunityIdea\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

/**
 * Options tree for "Categories" field
 */
class Options implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var array
     */
    protected $categoriesTree;

    /**
     * Options constructor.
     *
     * @param CategoryCollectionFactory $categoryCollectionFactory
     */
    public function __construct(CategoryCollectionFactory $categoryCollectionFactory)
    {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getCategoriesTree();
    }

     /**
     * @return array
     */
    public function getCategoriesArray()
    {
        if ($this->categoriesTree === null) {
            /* @var $collection \Magento\Catalog\Model\ResourceModel\Category\Collection */
            $collection = $this->categoryCollectionFactory->create();
            $arrayElements = array();
            $element = array();
            foreach ($collection as $category) {
             
                $elemnt[$category->getId()] = array(
                    'parent_id' => $category->getParentId(),
                    'value' => $category->getId(),
                    'is_active' => $category->getData('enabled'),
                    'label' =>  $category->getName(),
                );
                $arrayElements = $elemnt;
            }
            $this->categoriesTree = $arrayElements;
        }

        return $this->categoriesTree ;
    }
    /**
     * @return mixed
     */
    public function getCategoriesTree()
    {   
        $categories = $this->getCategoriesArray(); 
        $tree = $this->buildTree($categories);
        return $tree; 
    }
	/**
     * @return array
     */
    function buildTree(&$array) {
        $tree = array();
    
        // Create an associative array with each key being the ID of the item
        foreach($array as $k => &$v) {
          $tree[$v['value']] = &$v;
        }
    
        // Loop over the array and add each child to their parent
        foreach($tree as $k => &$v) {
            if(!$v['parent_id'] || $v['parent_id'] ==0) {
              continue;

            }
            $tree[$v['parent_id']]['optgroup'][] = &$v;
        }
    
        // Loop over the array again and remove any items that don't have a parent of 0;
        foreach($tree as $k => &$v) {
          if(!$v['parent_id']) {
            continue;
          }
          unset($tree[$k]);
        }
    
        return $tree;
    }
}
