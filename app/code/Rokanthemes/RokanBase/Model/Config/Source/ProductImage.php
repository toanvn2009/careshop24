<?php
/**
 * Copyright © 2019 The_Blue_Sky. All rights reserved.
 * @Author: Tony Pham
 * @Email: tonypham.web.developer@gmail.com
 */

namespace Rokanthemes\RokanBase\Model\Config\Source;

class ProductImage implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('Select Image')],
            ['value' => 'product_base_image', 'label' => __('Base Image')],
			['value' => 'product_small_image', 'label' => __('Small Image')],
			['value' => 'product_thumbnail_image', 'label' => __('Thumbnail Image')]
        ];
    }
}