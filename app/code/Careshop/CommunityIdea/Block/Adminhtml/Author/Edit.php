<?php

namespace Careshop\CommunityIdea\Block\Adminhtml\Author;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Careshop\CommunityIdea\Model\Author;

class Edit extends Container
{
    /**
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
     * Initialize Idea edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'user_id';
        $this->_blockGroup = 'Careshop_CommunityIdea';
        $this->_controller = 'adminhtml_author';

        parent::_construct();

        $this->buttonList->add('save-and-continue', [
            'label' => __('Save And Continue Edit'),
            'data_attribute' => [
                'mage-init' => [
                    'button' => [
                        'event' => 'saveAndContinueEdit',
                        'target' => '#edit_form'
                    ]
                ]
            ]
        ], -100);

        $this->buttonList->add(
            'delete',
            [
                'label' => __('Delete'),
                'class' => 'delete',
                'onclick' => "setLocation('{$this->getUrl('community/author/delete', [
                    'id' => $this->getCurrentAuthor()->getId(),
                    '_current' => true,
                    'back' => 'edit'
                ])}')",
            ],
            -101
        );
    }

    /**
     * Retrieve text for header element depending on loaded Idea
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var Author $author */
        $author = $this->getCurrentAuthor();
        if ($author->getId()) {
            return __("Edit Author '%1'", $this->escapeHtml($author->getName()));
        }

        return __('New Author');
    }

    /**
     * @return Author
     */
    public function getCurrentAuthor()
    {
        return $this->coreRegistry->registry('community_author');
    }
}
