<?php

namespace Careshop\CommunityDevelopProduct\Block\Adminhtml\Forum;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Careshop\CommunityDevelopProduct\Model\Forum;

/**
 * Class Edit
 * @package Careshop\CommunityIdea\Block\Adminhtml\Idea
 */
class Edit extends Container
{
    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * constructor
     *
     * @param Registry $coreRegistry
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Registry $coreRegistry,
        Context $context,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context, $data);
    }

    /**
     * Initialize Idea edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Careshop_CommunityDevelopProduct';
        $this->_controller = 'adminhtml_forum';

        parent::_construct();

        if (!$this->getRequest()->getParam('history')) {
            $develop = $this->coreRegistry->registry('community_develop_forum');
            $this->buttonList->remove('save');
            $classApply= 'apply_to_product';
        }
    }

    /**
     * Retrieve text for header element depending on loaded Idea
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var Idea $idea */
        $forum = $this->coreRegistry->registry('community_develop_forum');

        if ($this->getRequest()->getParam('history')) {
            return __("Edit History forum '%1'", $this->escapeHtml($forum->getName()));
        }

        if ($forum->getId() && $develop->getDuplicate()) {
            return __("Edit forum '%1'", $this->escapeHtml($forum->getName()));
        }

        return __('New forum');
    }
}
