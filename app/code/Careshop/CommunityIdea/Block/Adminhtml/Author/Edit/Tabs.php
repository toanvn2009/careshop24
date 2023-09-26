<?php

namespace Careshop\CommunityIdea\Block\Adminhtml\Author\Edit;

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

        $this->setId('author_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Author Information'));
    }
}
