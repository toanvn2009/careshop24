<?php

namespace Careshop\CommunityTesterProduct\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Class Idea
 * @package Careshop\CommunityIdea\Block\Adminhtml
 */
class Tester extends Container
{
    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_tester';
        $this->_blockGroup = 'Careshop_CommunityTesterProduct';
        $this->_headerText = __('Tester');
        $this->_headerText = __('Tester');
        $this->_addButtonLabel = __('Create New Tester');

        parent::_construct();
    }
}
