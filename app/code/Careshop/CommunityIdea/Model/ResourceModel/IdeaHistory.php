<?php

namespace Careshop\CommunityIdea\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Careshop\CommunityIdea\Helper\Data;

class IdeaHistory extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('community_idea_history', 'history_id');
    }

    /**
     * @param AbstractModel $object
     *
     * @return AbstractDb
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if (is_array($object->getData('store_ids'))) {
            $object->setData('store_ids', implode(',', $object->getData('store_ids')));
        }
        if (is_array($object->getData('categories_ids'))) {
            $object->setData('category_ids', implode(',', $object->getData('categories_ids')));
        }
        if (is_array($object->getData('topics_ids'))) {
            $object->setData('topic_ids', implode(',', $object->getData('topics_ids')));
        }
        if (is_array($object->getData('tags_ids'))) {
            $object->setData('tag_ids', implode(',', $object->getData('tags_ids')));
        }
        if (is_array($object->getData('products_data'))) {
            $data = $object->getData('products_data');
            foreach ($data as $key => $datum) {
                $data[$key]['position'] = $datum['position'] ?: '0';
            }

            $object->setData('product_ids', Data::jsonEncode($data));
        }

        return parent::_beforeSave($object);
    }

    protected function _afterLoad(AbstractModel $object)
    {
        $object->setData('categories_ids', explode(',', $object->getCategoryIds()));
        $object->setData('tags_ids', explode(',', $object->getTagIds()));
        $object->setData('topics_ids', explode(',', $object->getTopicIds()));

        return parent::_afterLoad($object);
    }
}
