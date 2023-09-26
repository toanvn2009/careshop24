<?php

namespace Careshop\CommunityIdea\Model\ResourceModel\Author\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult
{
    /**
     * @return $this|SearchResult|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->joinLeft(
            ['cus' => $this->getTable('customer_entity')],
            'main_table.customer_id = cus.entity_id',
            ['email']
        )->joinLeft(
            ['idea' => $this->getTable('community_idea')],
            'main_table.user_id = idea.author_id',
            ['qty_idae' => 'COUNT(idea_id)']
        )->group('main_table.user_id');

        $this->addFilterToMap('name', 'main_table.name');
        $this->addFilterToMap('url_key', 'main_table.url_key');

        return $this;
    }

    /**
     * @param array|string $field
     * @param null $condition
     *
     * @return SearchResult
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'qty_idae') {
            foreach ($condition as $key => $value) {
                if ($key === 'like') {
                    $this->getSelect()->having('COUNT(idea_id) LIKE ?', $value);
                }
            }
            return $this;
        }

        return parent::addFieldToFilter($field, $condition);
    }
}
