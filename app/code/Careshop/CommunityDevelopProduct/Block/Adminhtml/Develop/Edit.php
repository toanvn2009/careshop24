<?php

namespace Careshop\CommunityDevelopProduct\Block\Adminhtml\Develop;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Careshop\CommunityDevelopProduct\Model\Develop;

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
        $this->_controller = 'adminhtml_develop';

        parent::_construct();

        if (!$this->getRequest()->getParam('history')) {
            $develop = $this->coreRegistry->registry('community_develop');

            $this->buttonList->remove('save');
            $this->buttonList->remove('delete');
            $classApply= 'apply_to_product';
            $this->buttonList->add(
                'apply_to_product',
                [
                    'label' => __('Apply To Test The Product'),
                    'class' => $classApply,
                    'onclick' => sprintf("location.href = '%s';", $this->applyIdeaToProduct()),
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
        $develop = $this->coreRegistry->registry('community_develop');

        if ($this->getRequest()->getParam('history')) {
            return __("Edit History develop '%1'", $this->escapeHtml($develop->getName()));
        }

        if ($develop->getId() && $develop->getDuplicate()) {
            return __("Edit develop '%1'", $this->escapeHtml($develop->getName()));
        }

        return __('New develop');
    }

    protected function applyIdeaToProduct()
    {
        $develop = $this->coreRegistry->registry('community_develop');

        return $this->getUrl('*/*/applytest', ['id' => $develop->getId(), 'applytest' => true]);
    }
}
