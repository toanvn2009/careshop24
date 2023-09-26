<?php

namespace Careshop\CommunityIdea\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class SideBarLR implements ArrayInterface
{
    const LEFT = '2columns-left';
    const RIGHT = '2columns-right';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->toArray() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::LEFT => __('Left'),
            self::RIGHT => __('Right')
        ];
    }
}
