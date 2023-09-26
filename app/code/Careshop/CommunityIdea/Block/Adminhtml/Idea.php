<?php

namespace Careshop\CommunityIdea\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Class Idea
 * @package Careshop\CommunityIdea\Block\Adminhtml
 */
class Idea extends Container
{
    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_idea';
        $this->_blockGroup = 'Careshop_CommunityIdea';
        $this->_headerText = __('Ideas');
        $this->_headerText = __('Ideas');
        $this->_addButtonLabel = __('Create New Idea');

        parent::_construct();
    }
}
