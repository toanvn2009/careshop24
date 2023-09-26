<?php

namespace Careshop\CommunityIdea\Block\Adminhtml\Topic;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Careshop\CommunityIdea\Model\Topic;

class Edit extends Container
{
    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context, $data);
    }

    /**
     * Initialize Topic edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Careshop_CommunityIdea';
        $this->_controller = 'adminhtml_topic';

        parent::_construct();

        $this->buttonList->add(
            'save-and-continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
    }

    /**
     * Retrieve text for header element depending on loaded Topic
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var Topic $topic */
        $topic = $this->coreRegistry->registry('community_topic');
        if ($topic->getId()) {
            return __("Edit Topic '%1'", $this->escapeHtml($topic->getName()));
        }

        return __('New Topic');
    }
}
