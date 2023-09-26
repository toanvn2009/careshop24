<?php

namespace Careshop\CommunityIdea\Controller\Adminhtml\History;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Careshop\CommunityIdea\Controller\Adminhtml\History;
use Careshop\CommunityIdea\Model\IdeaFactory;
use Careshop\CommunityIdea\Model\IdeaHistory;
use Careshop\CommunityIdea\Model\IdeaHistoryFactory;

class Edit extends History
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
     * @param IdeaHistoryFactory $ideaHistoryFactory
     * @param IdeaFactory $ideaFactory
     * @param Registry $coreRegistry
     * @param DateTime $date
     * @param PageFactory $resultPageFactory
     * @param Context $context
     */
    public function __construct(
        IdeaHistoryFactory $ideaHistoryFactory,
        IdeaFactory $ideaFactory,
        Registry $coreRegistry,
        DateTime $date,
        PageFactory $resultPageFactory,
        Context $context
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($ideaHistoryFactory, $ideaFactory, $coreRegistry, $date, $context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|Redirect|Page
     */
    public function execute()
    {
        /** @var IdeaHistory $history */
        $history = $this->initIdeaHistory();

        if (!$history) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*');

            return $resultRedirect;
        }

        $this->coreRegistry->register('community_idea', $history);

        /** @var \Magento\Backend\Model\View\Result\Page|Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Careshop_CommunityIdea::history');
        $resultPage->getConfig()->getTitle()->set(__('Idea History'));

        $resultPage->getConfig()->getTitle()->prepend(__('Edit Idea History'));

        return $resultPage;
    }
}
