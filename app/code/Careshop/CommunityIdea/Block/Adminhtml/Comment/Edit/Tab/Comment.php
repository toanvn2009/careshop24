<?php

namespace Careshop\CommunityIdea\Block\Adminhtml\Comment\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\System\Store;
use Careshop\CommunityIdea\Model\Config\Source\Comments\Status;
use Careshop\CommunityIdea\Model\IdeaFactory;

class Comment extends Generic implements TabInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $_customerRepository;

    /**
     * @var IdeaFactory
     */
    protected $_ideaFactory;

    /**
     * @var Status
     */
    protected $_commentStatus;

    /**
     * @var Store
     */
    protected $systemStore;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Comment constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param IdeaFactory $ideaFactory
     * @param Status $commentStatus
     * @param Store $systemStore
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        CustomerRepositoryInterface $customerRepository,
        IdeaFactory $ideaFactory,
        Status $commentStatus,
        Store $systemStore,
        array $data = []
    ) {
        $this->_commentStatus = $commentStatus;
        $this->_customerRepository = $customerRepository;
        $this->_ideaFactory = $ideaFactory;
        $this->systemStore = $systemStore;
        $this->storeManager = $context->getStoreManager();

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Generic
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function _prepareForm()
    {
        $comment = $this->_coreRegistry->registry('community_comment');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('comment_');
        $form->setFieldNameSuffix('comment');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Comment Details'), 'class' => 'fieldset-wide']
        );

        if ($comment->getId()) {
            $fieldset->addField('comment_id', 'hidden', ['name' => 'comment_id']);
        }

        $idea = $this->_ideaFactory->create()->load($comment->getIdeaId());
        $ideaText = '<a href="' . $this->getUrl('community/idea/edit', ['id' => $comment->getIdeaId()])
            . '" onclick="this.target=\'blank\'">' . $this->escapeHtml($idea->getName()) . '</a>';
        $fieldset->addField('idea_name', 'note', ['text' => $ideaText, 'label' => __('Idea'), 'name' => 'idea_name']);

        if ($comment->getEntityId() > 0) {
            $customer = $this->_customerRepository->getById($comment->getEntityId());
            $customerText = '<a href="'
                . $this->getUrl(
                    'customer/index/edit',
                    ['id' => $customer->getId(), 'active_tab' => 'review']
                )
                . '" onclick="this.target=\'blank\'">'
                . $this->escapeHtml($customer->getFirstname() . ' ' . $customer->getLastname())
                . '</a> <a href="mailto:%4">(' . $customer->getEmail() . ')</a>';
        } else {
            $customerText = 'Guest';
        }

        $fieldset->addField(
            'customer_name',
            'note',
            ['text' => $customerText, 'label' => __('Customer'), 'name' => 'customer_name']
        );

        $fieldset->addField('status', 'select', [
            'label' => __('Status'),
            'required' => true,
            'name' => 'status',
            'values' => $this->_commentStatus->toArray()
        ]);
        $fieldset->addField('content', 'textarea', [
            'label' => __('Content'),
            'required' => true,
            'name' => 'content',
            'style' => 'height:24em;'
        ]);
        $viewText = '';
        foreach ($this->storeManager->getStores() as $store) {
            if ($store->getId() === $comment->getStoreIds()) {
                $viewText .= '<a href="' . $idea->getUrl($store->getId()) . '#cmt-id-' . $comment->getId()
                    . '" onclick="this.target=\'blank\'">View in store ' . $store->getName() . '</a><br>';
            }
        }

        $fieldset->addField(
            'view_front',
            'note',
            ['text' => $viewText, 'label' => __('View On Front End'), 'name' => 'view_front']
        );

        $form->addValues($comment->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Comment');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
