<?php

namespace Careshop\CommunityIdea\Controller\Adminhtml\Idea;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Careshop\CommunityIdea\Controller\Adminhtml\Idea;
use Careshop\CommunityIdea\Model\IdeaFactory;

class Edit extends Idea
{
    /**
     * Page factory
     *
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param IdeaFactory $ideaFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        IdeaFactory $ideaFactory,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($ideaFactory, $registry, $context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|Redirect|Page
     */
    public function execute()
    {
        /** @var \Careshop\CommunityIdea\Model\Idea $idea */
        $idea = $this->initIdea();
        $duplicate = $this->getRequest()->getParam('duplicate');

        if (!$idea) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*');

            return $resultRedirect;
        }

        $data = $this->_session->getData('community_idea_data', true);
        if (!empty($data)) {
            $idea->setData($data);
        }

        $this->coreRegistry->register('community_idea', $idea);

        /** @var \Magento\Backend\Model\View\Result\Page|Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Careshop_CommunityIdea::idea');
        $resultPage->getConfig()->getTitle()->set(__('Ideas'));

        $title = $idea->getId() && !$duplicate ? $idea->getName() : __('New Idea');
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
