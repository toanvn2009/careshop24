<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Rokanthemes\RokanBase\Block\Product;

class Review extends \Magento\Review\Block\Product\Review
{
    public function setTabTitle()
    {
        $title = $this->getCollectionSize()
            ? __('Review & Improve', '<span class="counter">' . $this->getCollectionSize() . '</span>')
            : __('Review & Improve');
        $this->setTitle($title);
    }
}
