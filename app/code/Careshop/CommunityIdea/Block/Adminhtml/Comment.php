<?php

namespace Careshop\CommunityIdea\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Comment extends Container
{
    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_comment';
        $this->_blockGroup = 'Careshop_CommunityIdea';
        $this->_addButtonLabel = __('New Comment');

        parent::_construct();
    }
}
