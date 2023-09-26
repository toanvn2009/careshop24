<?php

namespace Careshop\CommunityIdea\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Topic extends Container
{
    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_topic';
        $this->_blockGroup = 'Careshop_CommunityIdea';
        $this->_headerText = __('Topics');
        $this->_addButtonLabel = __('Create New Topic');

        parent::_construct();
    }
}
