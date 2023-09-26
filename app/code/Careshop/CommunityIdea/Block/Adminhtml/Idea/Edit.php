<?php

namespace Careshop\CommunityIdea\Block\Adminhtml\Idea;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Careshop\CommunityIdea\Model\Idea;

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
        $this->_blockGroup = 'Careshop_CommunityIdea';
        $this->_controller = 'adminhtml_idea';

        parent::_construct();

        if (!$this->getRequest()->getParam('history')) {
            $idea = $this->coreRegistry->registry('community_idea');
            $this->buttonList->remove('save'); 
            $this->buttonList->add(
                'save',
                [
                    'label' => __('Save'),
                    'class' => 'save primary',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => [
                                'event' => 'save',
                                'target' => '#edit_form'
                            ]
                        ]
                    ],
                    'class_name' => \Magento\Ui\Component\Control\Container::SPLIT_BUTTON,
                    'options' => $this->getOptions($idea),
                ],
                -100
            );

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
            if($idea->getApplyToProduct() == 1) 
            {
                $classApply= 'apply_to_product disabled';
            } else {
                $classApply= 'apply_to_product';
            }
            $this->buttonList->add(
                'apply_to_product',
                [
                    'label' => __('Apply To Product'),
                    'class' => $classApply,
                    'onclick' => sprintf("location.href = '%s';", $this->applyIdeaToProduct()),
                ],
                -101
            );

        }
    }

    /**
     * Retrieve options
     *
     * @param Idea $idea
     *
     * @return array
     */
    protected function getOptions($idea)
    {
        if ($idea->getId() && !$this->_request->getParam('duplicate')) {
            $options[] = [
                'id_hard' => 'save_and_draft',
                'label' => __('Save as Draft'),
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'save',
                            'target' => '#edit_form',
                            'eventData' => [
                                'action' => ['args' => ['action' => 'draft']]
                            ],
                        ]
                    ]
                ]
            ];
        }
        $options[] = [
            'id_hard' => 'save_and_history',
            'label' => __(' Save & add History'),
            'data_attribute' => [
                'mage-init' => [
                    'button' => [
                        'event' => 'save',
                        'target' => '#edit_form',
                        'eventData' => [
                            'action' => ['args' => ['action' => 'add']]
                        ],
                    ]
                ]
            ]
        ];

        return $options;
    }

    /**
     * Retrieve text for header element depending on loaded Idea
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var Idea $idea */
        $idea = $this->coreRegistry->registry('community_idea');

        if ($this->getRequest()->getParam('history')) {
            return __("Edit History Idea '%1'", $this->escapeHtml($idea->getName()));
        }

        if ($idea->getId() && $idea->getDuplicate()) {
            return __("Edit Idea '%1'", $this->escapeHtml($idea->getName()));
        }

        return __('New Idea');
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        /** @var Idea $idea */
        $idea = $this->coreRegistry->registry('community_idea');
        if ($idea->getId()) {
            if ($idea->getDuplicate()) {
                $ar = [];
            } else {
                $ar = ['id' => $idea->getId()];
            }
            if ($this->getRequest()->getParam('history')) {
                $ar['idea_id'] = $this->getRequest()->getParam('idea_id');
            }

            return $this->getUrl('*/*/save', $ar);
        }

        return parent::getFormActionUrl();
    }

    /**
     * @return string
     */
    protected function getDuplicateUrl()
    {
        $idea = $this->coreRegistry->registry('community_idea');

        return $this->getUrl('*/*/duplicate', ['id' => $idea->getId(), 'duplicate' => true]);
    }

    protected function applyIdeaToProduct()
    {
        $idea = $this->coreRegistry->registry('community_idea');

        return $this->getUrl('*/*/applyidea', ['id' => $idea->getId(), 'applyidea' => true]);
    }

    /**
     * @return string
     */
    protected function getSaveDraftUrl()
    {
        return $this->getUrl('*/*/save', ['action' => 'draft']);
    }

    /**
     * @return string
     */
    protected function getSaveAddHistoryUrl()
    {
        return $this->getUrl('*/*/save', ['action' => 'add']);
    }
}
