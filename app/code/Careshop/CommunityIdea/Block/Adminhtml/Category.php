<?php

namespace Careshop\CommunityIdea\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;


class Category extends Container
{
    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_category';
        $this->_blockGroup = 'Careshop_CommunityIdea';
        $this->_headerText = __('Categories');
        $this->_addButtonLabel = __('Create New Community Category');

        parent::_construct();
    }
}
