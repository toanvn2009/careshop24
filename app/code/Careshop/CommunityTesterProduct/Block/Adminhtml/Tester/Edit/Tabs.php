<?php

namespace Careshop\CommunityTesterProduct\Block\Adminhtml\Tester\Edit;

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

        $this->setId('tester_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Tester Information'));
    }
}
