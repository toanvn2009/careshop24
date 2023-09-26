<?php
/**
 * Copyright © 2019 The_Blue_Sky. All rights reserved.
 * @Author: Tony Pham
 * @Email: tonypham.web.developer@gmail.com
 */

namespace Rokanthemes\RokanBase\Model\Config\Source;

class ListMode implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'square', 'label' => __('Square ')],
			['value' => 'circle', 'label' => __('Circle')]
        ];
    }
}