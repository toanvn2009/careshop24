<?php

namespace Careshop\CommunityIdea\Model\Config\Source\Comments;

use Magento\Framework\Option\ArrayInterface;

class Type implements ArrayInterface
{
    const DEFAULT_COMMENT = 1;
    const FACEBOOK = 2;
    const DISQUS = 3;
    const DISABLE = 4;

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
        return [
            self::DEFAULT_COMMENT => __('Default Comment'),
            self::DISQUS => __('Disqus Comment'),
            self::FACEBOOK => __('Facebook Comment'),
            self::DISABLE => __('Disable Completely')
        ];
    }
}
