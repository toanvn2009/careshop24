<?php

namespace Careshop\CommunityDevelopProduct\Block\Adminhtml\Forum\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('forum_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Forum Information'));
    }
}
