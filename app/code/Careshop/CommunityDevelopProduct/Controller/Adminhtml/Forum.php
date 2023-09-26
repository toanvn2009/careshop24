<?php

namespace Careshop\CommunityDevelopProduct\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Careshop\CommunityDevelopProduct\Model\ForumFactory;
use Careshop\CommunityDevelopProduct\Model\CommentForumFactory;
use Careshop\CommunityDevelopProduct\Model\ReportForumFactory;

abstract class Forum extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'Careshop_CommunityDevelopProduct::develop_forum';

    public $forumFactory;
    public $commentForumFactory;
    public $reportForumFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * Idea constructor.
     *
     * @param DevelopFactory $developFactory
     * @param Registry $coreRegistry
     * @param Context $context
     */
    public function __construct(
        ForumFactory $forumFactory,
        Registry $coreRegistry,
        Context $context
    ) {
        $this->forumFactory = $forumFactory;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * @param bool $register
     * @param bool $isSave
     *
     * @return bool|\Careshop\CommunityIdea\Model\Idea
     */
    protected function initForum($register = false, $isSave = false) 
    { 
        $developForumId = (int)$this->getRequest()->getParam('id');
        $developForum = $this->forumFactory->create();
        $developForum->load($developForumId);
        if ($register) {
            $this->coreRegistry->register('community_develop_forum', $developForum);
        }
        return $developForum;
    }
}
