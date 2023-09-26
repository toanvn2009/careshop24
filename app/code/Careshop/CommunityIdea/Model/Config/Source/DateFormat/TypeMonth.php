<?php

namespace Careshop\CommunityIdea\Model\Config\Source\DateFormat;

use Magento\Framework\Option\ArrayInterface;

class TypeMonth implements ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $dateArray = [];
        $type = ['F , Y', 'Y - m', 'm / Y', 'M  Y'];
        foreach ($type as $item) {
            $dateArray [] = [
                'value' => $item,
                'label' => $item . ' (' . date($item) . ')'
            ];
        }

        return $dateArray;
    }
}
