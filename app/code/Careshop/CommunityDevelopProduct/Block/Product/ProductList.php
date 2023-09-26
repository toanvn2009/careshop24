<?php 
namespace Careshop\CommunityDevelopProduct\Block\Product;

use Careshop\CommunityDevelopProduct\Model\DevelopFactory;
use Careshop\CommunityDevelopProduct\Model\CommentForumFactory;

class ProductList extends \Magento\Catalog\Block\Product\ProductList\Item\Block
{

    /**
     * @var CommentForumFactory
     */
    public $commentForum;

    /**
     * @var DevelopFactory
     */
    protected $develop;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
         array $data = [],
         DevelopFactory $develop,
         CommentForumFactory $commentForum
    )
    {
        $this->develop        = $develop;
        $this->commentForum        = $commentForum;
        parent::__construct($context, $data);
    }

    public function getResourceDevelop()
    {
        $resource = $this->develop->create()->getResource();
        return $resource;
    }

    public function getDevelopProduct($product_id)
    {
        $data = $this->getResourceDevelop()->getDevelop($product_id); 
        return $data;
    }

    public function getDevelopComment($product_id)
    {
        $data = $this->getResourceDevelop()->getDevelopComment($product_id); 
        return $data;
    }

}