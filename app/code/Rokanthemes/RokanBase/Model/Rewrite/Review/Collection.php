<?php
/**
 * Copyright Â© Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Rokanthemes\RokanBase\Model\Rewrite\Review;

/**
 * Review collection resource model
 *
 * @api
 * @since 100.0.2
 */
class Collection extends \Magento\Review\Model\ResourceModel\Review\Collection
{
    /**
     * Initialize select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        $filter = isset($_GET['filter'])?$_GET['filter']:'';
        $this->getSelect()->join(
            ['review_detail' => $this->getReviewDetailTable()],
            'main_table.review_id = review_detail.review_id',
            ['replay','replay_date']
        );
        if ($filter && $filter !== 'all') {
            $array = explode("_",$filter);
            $this->getSelect()->joinLeft(
                ['vote' => 'rating_option_vote'],
                'main_table.review_id = vote.review_id',
                ['vote.value']
            )->where('vote.value IN (?)', $array);
        }
        return parent::_initSelect();
    }
}