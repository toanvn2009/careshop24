<?php

namespace Careshop\CommunityDevelopProduct\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Class Idea
 * @package Careshop\CommunityIdea\Block\Adminhtml
 */
class Develop extends Container
{
    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_develop';
        $this->_blockGroup = 'Careshop_CommunityDevelopProduct';
        $this->_headerText = __('Develop');
        $this->_headerText = __('Develop');
        $this->_addButtonLabel = __('Create New Develop');

        parent::_construct();
    }
}
