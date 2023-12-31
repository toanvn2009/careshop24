<?php

namespace Careshop\CommunityIdea\Block\Adminhtml\Idea\Edit\Tab\Renderer\History;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\Store as CoreStore;
use Magento\Framework\DataObject;
use Magento\Framework\Phrase;


class Store extends CoreStore
{
    /**
     * Render row store views
     *
     * @param DataObject $row
     *
     * @return Phrase|string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function render(DataObject $row)
    {
        $out = '';
        $skipAllStoresLabel = $this->_getShowAllStoresLabelFlag();
        $skipEmptyStoresLabel = $this->_getShowEmptyStoresLabelFlag();
        $origStores = $row->getData($this->getColumn()->getIndex());

        if ($origStores === '0') {
            $origStores = [0 => '0'];
        }

        if ($origStores === null && $row->getStoreName()) {
            $scopes = [];
            foreach (explode("\n", $row->getStoreName()) as $k => $label) {
                $scopes[] = str_repeat('&nbsp;', $k * 3) . $label;
            }
            $out .= implode('<br/>', $scopes) . __(' [deleted]');

            return $out;
        }

        if (empty($origStores) && !$skipEmptyStoresLabel) {
            return '';
        }
        if (!is_array($origStores)) {
            $origStores = [$origStores];
        }

        if (empty($origStores)) {
            return '';
        } elseif (!$skipAllStoresLabel && in_array('0', $origStores, true) && count($origStores) === 1) {
            return __('All Store Views');
        }

        $data = $this->_getStoreModel()->getStoresStructure(false, $origStores);

        foreach ($data as $website) {
            $out .= $website['label'] . '<br/>';
            foreach ($website['children'] as $group) {
                $out .= str_repeat('&nbsp;', 3) . $group['label'] . '<br/>';
                foreach ($group['children'] as $store) {
                    $out .= str_repeat('&nbsp;', 6) . $store['label'] . '<br/>';
                }
            }
        }

        return $out;
    }
}
