<?php
/**
 * Copyright © 2019 The_Blue_Sky. All rights reserved.
 * @Author: Tony Pham
 * @Email: tonypham.web.developer@gmail.com
 */

namespace Rokanthemes\RokanBase\Model\Config\Source;

class ProductTypes implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'simple', 'label' => __('Simple')],
			['value' => 'virtual', 'label' => __('Virtual')],
			['value' => 'downloadable', 'label' => __('Downloadable')],
			['value' => 'configurable', 'label' => __('Configurable')],
            ['value' => 'grouped', 'label' => __('Grouped')],
            ['value' => 'bundle', 'label' => __('Bundle')]
        ];
    }
}