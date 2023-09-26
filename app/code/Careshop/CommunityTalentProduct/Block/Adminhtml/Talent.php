<?php

namespace Careshop\CommunityTalentProduct\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Class Idea
 * @package Careshop\CommunityIdea\Block\Adminhtml
 */
class Talent extends Container
{
    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_talent';
        $this->_blockGroup = 'Careshop_CommunityTalentProduct';
        $this->_headerText = __('Talent');
        $this->_headerText = __('Talent');
        $this->_addButtonLabel = __('Create New Talent');

        parent::_construct();
    }
}
