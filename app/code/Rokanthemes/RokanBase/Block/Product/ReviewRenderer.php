<?php
namespace Rokanthemes\RokanBase\Block\Product;

class ReviewRenderer extends \Magento\Review\Block\Product\ReviewRenderer
{
	protected $_reviewsCollection;
	
	public function getReviewsCollection(){
		if (null === $this->_reviewsCollection) { 
            $this->_reviewsCollection = $this->_reviewFactory->create()->getCollection()->addStoreFilter(
                $this->_storeManager->getStore()->getId()
            )->addStatusFilter(
                \Magento\Review\Model\Review::STATUS_APPROVED
            )->addEntityFilter(
                'product',
                $this->getProduct()->getId()
            )->addRateVotes();
        }  
        return $this->_reviewsCollection;
	}
}
