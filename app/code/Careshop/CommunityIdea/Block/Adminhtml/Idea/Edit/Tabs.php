<?php

namespace Careshop\CommunityIdea\Block\Adminhtml\Idea\Edit;

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

        $this->setId('idea_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Idea Information'));
    }
}
