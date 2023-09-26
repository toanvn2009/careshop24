<?php

namespace Careshop\CommunityTesterProduct\Block\Adminhtml\Tester;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Careshop\CommunityTesterProduct\Model\Tester;

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
        $this->_blockGroup = 'Careshop_CommunityTesterProduct';
        $this->_controller = 'adminhtml_tester';

        parent::_construct();

        if (!$this->getRequest()->getParam('history')) {
            $tester = $this->coreRegistry->registry('community_tester');

            $this->buttonList->remove('save');
            $this->buttonList->remove('delete');
            $classApply= 'apply_to_product';
            $this->buttonList->add(
                'apply_to_product',
                [
                    'label' => __('Apply To Talent The Product'),
                    'class' => $classApply,
                    'onclick' => sprintf("location.href = '%s';", $this->applyTesterToTalent()),
                ],
                -101
            );

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
        $tester = $this->coreRegistry->registry('community_tester');

        if ($this->getRequest()->getParam('history')) {
            return __("Edit History tester '%1'", $this->escapeHtml($tester->getName()));
        }

        if ($tester->getId() && $tester->getDuplicate()) {
            return __("Edit tester '%1'", $this->escapeHtml($tester->getName()));
        }

        return __('New tester');
    }

    protected function applyTesterToTalent()
    {
        $tester = $this->coreRegistry->registry('community_tester');

        return $this->getUrl('*/*/applytalent', ['id' => $tester->getId(), 'applytalent' => true]);
    }
}
