<?php

namespace Careshop\CommunityIdea\Model\Config\Source\Comments\Facebook;

use Magento\Framework\Option\ArrayInterface;

class Colorscheme implements ArrayInterface
{
    const LIGHT = 'light';
    const DARK = 'dark';

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
        return [self::LIGHT => __('Light'), self::DARK => __('Dark')];
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}
