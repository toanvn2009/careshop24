<?php

namespace Careshop\CommunityIdea\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class DisplayType implements ArrayInterface
{
    const LIST_VIEW = 1;
    const GRID = 2;

    /**
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
        return [self::LIST_VIEW => __('List View'), self::GRID => __('Grid View')];
    }
}
