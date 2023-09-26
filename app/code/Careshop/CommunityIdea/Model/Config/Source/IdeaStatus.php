<?php


namespace Careshop\CommunityIdea\Model\Config\Source;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Option\ArrayInterface;

class IdeaStatus implements ArrayInterface
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        // You can write your code to fetch email values from custom table and convert it to as value => label pair
        return [
            '1' => 'Orange',
            '2' => 'Black',
            '3' => 'Blue',
        ];
    }

}
