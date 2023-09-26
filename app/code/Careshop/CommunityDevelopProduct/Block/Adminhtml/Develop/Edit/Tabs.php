<?php

namespace Careshop\CommunityDevelopProduct\Block\Adminhtml\Develop\Edit;

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

        $this->setId('develop_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Develop Information'));
    }
}
