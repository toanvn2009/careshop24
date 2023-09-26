<?php
/**
 * Copyright © 2019 The_Blue_Sky. All rights reserved.
 * @Author: Tony Pham
 * @Email: tonypham.web.developer@gmail.com
 */

namespace Rokanthemes\RokanBase\Model\Config\Source;

class DisplayMode implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'grid', 'label' => __('Grid')],
			['value' => 'list', 'label' => __('List')]
        ];
    }
}