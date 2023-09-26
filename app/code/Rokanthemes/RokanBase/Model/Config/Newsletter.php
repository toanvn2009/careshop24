<?php
namespace Rokanthemes\RokanBase\Model\Config;

class Newsletter implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '0', 'label' => __('Disable')], 
            ['value' => '1', 'label' => __('Enable')], 
        ];
    }

    public function toArray()
    {
        return [
            '0' => __('Disable'), 
            '1' => __('Enable')
        ];
    }
}
