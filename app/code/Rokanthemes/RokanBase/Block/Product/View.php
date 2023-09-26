<?php
namespace Rokanthemes\RokanBase\Block\Product;

class View extends \Magento\Review\Block\Product\View
{
    public function getReviewsCollection()
    {
		$page = ($this->getRequest()->getParam('sort'))? $this->getRequest()->getParam('sort') : '';
        if (null === $this->_reviewsCollection) {
			if($page == 'lowest-to-highest'){
				$this->_reviewsCollection = $this->_reviewsColFactory->create()->addStoreFilter(
					$this->_storeManager->getStore()->getId()
				)->addStatusFilter(
					\Magento\Review\Model\Review::STATUS_APPROVED
				)->addEntityFilter(
					'product',
					$this->getProduct()->getId()
				);
				$this->_reviewsCollection = $this->_reviewsCollection->join(
					['rating_option_vote'],
					'main_table.review_id = rating_option_vote.review_id',
					['percent']
				)->setOrder('percent','ASC');
			}elseif($page == 'highest-to-lowest'){
				$this->_reviewsCollection = $this->_reviewsColFactory->create()->addStoreFilter(
					$this->_storeManager->getStore()->getId()
				)->addStatusFilter(
					\Magento\Review\Model\Review::STATUS_APPROVED
				)->addEntityFilter(
					'product',
					$this->getProduct()->getId()
				);
				$this->_reviewsCollection = $this->_reviewsCollection->join(
					['rating_option_vote'],
					'main_table.review_id = rating_option_vote.review_id',
					['percent'] 
				)->setOrder('percent','desc');
			}elseif($page == 'most-recent'){
				$this->_reviewsCollection = $this->_reviewsColFactory->create()->addStoreFilter(
					$this->_storeManager->getStore()->getId()
				)->addStatusFilter(
					\Magento\Review\Model\Review::STATUS_APPROVED
				)->addEntityFilter(
					'product',
					$this->getProduct()->getId()
				)->setOrder('review_id','desc')->setDateOrder();
			}else{
				$this->_reviewsCollection = $this->_reviewsColFactory->create()->addStoreFilter(
					$this->_storeManager->getStore()->getId()
				)->addStatusFilter(
					\Magento\Review\Model\Review::STATUS_APPROVED
				)->addEntityFilter(
					'product',
					$this->getProduct()->getId()
				);
				$this->_reviewsCollection = $this->_reviewsCollection->join(
					['rating_option_vote'],
					'main_table.review_id = rating_option_vote.review_id',
					['percent']
				)->setOrder('percent','desc');
			}
            
        }
        return $this->_reviewsCollection;
    }
 
	public function getProductId(){
		return  $this->getProduct()->getId();
	}
}
