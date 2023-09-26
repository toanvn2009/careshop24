<?php

namespace Careshop\CommunityTalentProduct\Block\Adminhtml\Talent;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Careshop\CommunityTalentProduct\Model\Talent;

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
        $this->_blockGroup = 'Careshop_CommunityTalentProduct';
        $this->_controller = 'adminhtml_talent';

        parent::_construct();

        if (!$this->getRequest()->getParam('history')) {
            $talent = $this->coreRegistry->registry('community_talent');

            $this->buttonList->remove('save');
            $this->buttonList->remove('delete');
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
        $talent = $this->coreRegistry->registry('community_talent');

        if ($this->getRequest()->getParam('history')) {
            return __("Edit History talent '%1'", $this->escapeHtml($talent->getName()));
        }

        if ($talent->getId() && $talent->getDuplicate()) {
            return __("Edit talent '%1'", $this->escapeHtml($talent->getName()));
        }

        return __('New talent');
    }
}
