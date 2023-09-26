<?php

namespace Careshop\CommunityTalentProduct\Block\Adminhtml\Talent\Edit;

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

        $this->setId('Talent_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Talent Information'));
    }
}
