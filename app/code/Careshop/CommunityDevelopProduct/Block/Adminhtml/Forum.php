<?php

namespace Careshop\CommunityDevelopProduct\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Class Idea
 * @package Careshop\CommunityIdea\Block\Adminhtml
 */
class Forum extends Container
{
    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_forum';
        $this->_blockGroup = 'Careshop_CommunityDevelopProduct';
        $this->_headerText = __('Forum');
        $this->_headerText = __('Forum');
        $this->_addButtonLabel = __('Create New Forum');

        parent::_construct();
    }
}
