<?php
 
namespace Rokanthemes\Reviews\Model;
 
class Review extends \Magento\Framework\Model\AbstractModel
{
	protected $objectManager;  
	protected $_resource;  
	protected $customerSession;  

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\App\ResourceConnection $resource
    ) {
		$this->objectManager = $objectManager;
        $this->_resource = $resource;
		$this->customerSession  = $customerSession;
    }
	
	public function dataReviewsPublished()
	{ 
		$adapter  = $this->_resource->getConnection(); 
		$customer_id = $this->customerSession->getId();
		$sql = 'SELECT review.*,review_detail.* FROM review INNER JOIN review_detail ON review.review_id = review_detail.review_id WHERE review_detail.customer_id="'.$customer_id.'" AND review.status_id="1"';
		$data_query = $adapter->fetchAll($sql);
		return $data_query;
	}
	
	public function dataReviewsRating($id)
	{ 
		$adapter  = $this->_resource->getConnection(); 
		$sql = 'SELECT percent FROM rating_option_vote WHERE review_id="'.$id.'"';
		$data_query = $adapter->fetchRow($sql);
		return $data_query['percent'];
	}
	public function dataReviewsByProductId($product_id)
	{ 
		$adapter  = $this->_resource->getConnection(); 
		$customer_id = $this->customerSession->getId();
		$sql = 'SELECT review.review_id,review.entity_simple_id,review_detail.review_id,review_detail.customer_id FROM review INNER JOIN review_detail ON review.review_id = review_detail.review_id WHERE review.entity_simple_id="'.$product_id.'" AND review_detail.customer_id="'.$customer_id.'"';
		$data_query = $adapter->fetchRow($sql);
		return $data_query;
	}
	public function dataImproveReviewByProductId($product_id) 
	{ 
		$adapter  = $this->_resource->getConnection(); 
		$customer_id = $this->customerSession->getId();
		$sql_improve = 'SELECT entity_id FROM improve_review WHERE customer_id="'.$customer_id.'" AND product_id_key="'.$product_id.'"';
		$data_query_improve = $adapter->fetchRow($sql_improve);
		return $data_query_improve;
	}
}