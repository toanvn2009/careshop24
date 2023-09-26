<?php

namespace Careshop\CommunityIdea\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Careshop\CommunityIdea\Model\IdeaFactory;
use Careshop\CommunityIdea\Model\IdeaHistory;
use Careshop\CommunityIdea\Model\IdeaHistoryFactory;

abstract class History extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'Careshop_CommunityIdea::idea';

    /**
     * Idea History Factory
     *
     * @var IdeaHistoryFactory
     */
    public $ideaHistoryFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * @var IdeaFactory
     */
    protected $ideaFactory;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * Idea constructor.
     *
     * @param IdeaHistoryFactory $ideaHistoryFactory
     * @param IdeaFactory $ideaFactory
     * @param Registry $coreRegistry
     * @param DateTime $date
     * @param Context $context
     */
    public function __construct(
        IdeaHistoryFactory $ideaHistoryFactory,
        IdeaFactory $ideaFactory,
        Registry $coreRegistry,
        DateTime $date,
        Context $context
    ) {
        $this->ideaHistoryFactory = $ideaHistoryFactory;
        $this->ideaFactory = $ideaFactory;
        $this->coreRegistry = $coreRegistry;
        $this->date = $date;

        parent::__construct($context);
    }

    /**
     * @param bool $register
     *
     * @return bool|IdeaHistory
     */
    protected function initIdeaHistory($register = false)
    {
        $historyId = (int)$this->getRequest()->getParam('id');

        /** @var \Careshop\CommunityIdea\Model\Idea $idea */
        $history = $this->ideaHistoryFactory->create();
        if ($historyId) {
            $history->load($historyId);
            if (!$history->getId()) {
                $this->messageManager->addErrorMessage(__('This History no longer exists.'));

                return false;
            }
        }

        if ($register) {
            $this->coreRegistry->register('community_idea_history', $history);
        }

        return $history;
    }
}
