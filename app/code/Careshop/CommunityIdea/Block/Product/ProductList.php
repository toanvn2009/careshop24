<?php 
namespace Careshop\CommunityIdea\Block\Product;
use Careshop\CommunityIdea\Model\LikeFactory;
use Careshop\CommunityIdea\Model\Idea;
use Careshop\CommunityIdea\Model\IdeaFactory;
use Careshop\CommunityIdea\Model\IdeaLikeFactory;
class ProductList extends \Magento\Catalog\Block\Product\ProductList\Item\Block
{

    
    /**
     * @var LikeFactory
     */
    public $likeFactory;

    /**
     * @var IdeaFactory
     */
    protected $ideaFactory;

    /**
     * @var IdeaLikeFactory
     */
    protected $ideaLikeFactory;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
         array $data = [],
         LikeFactory $likeFactory,
         IdeaFactory $ideaFactory,
         IdeaLikeFactory $ideaLikeFactory
    )
    {
        $this->likeFactory        = $likeFactory;
        $this->ideaFactory        = $ideaFactory;
        $this->ideaLikeFactory    = $ideaLikeFactory;

        parent::__construct($context, $data);
    }


}