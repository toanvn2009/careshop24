<?php

namespace Careshop\CommunityIdea\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Careshop\CommunityIdea\Model\IdeaFactory;

abstract class Idea extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'Careshop_CommunityIdea::idea';

    /**
     * Idea Factory
     *
     * @var IdeaFactory
     */
    public $ideaFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * Idea constructor.
     *
     * @param IdeaFactory $ideaFactory
     * @param Registry $coreRegistry
     * @param Context $context
     */
    public function __construct(
        IdeaFactory $ideaFactory,
        Registry $coreRegistry,
        Context $context
    ) {
        $this->ideaFactory = $ideaFactory;
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context);
    }

    /**
     * @param bool $register
     * @param bool $isSave
     *
     * @return bool|\Careshop\CommunityIdea\Model\Idea
     */
    protected function initIdea($register = false, $isSave = false)
    {
        $ideaId = (int)$this->getRequest()->getParam('id');
        $duplicate = $this->getRequest()->getParam('post')['duplicate'] ?? null;

        /** @var \Careshop\CommunityIdea\Model\Idea $idea */
        $idea = $this->ideaFactory->create();
        if ($ideaId) {
            if (!$isSave || !$duplicate) {
                $idea->load($ideaId);
                if (!$idea->getId()) {
                    $this->messageManager->addErrorMessage(__('This post no longer exists.'));

                    return false;
                }
            }
        }

        if (!$idea->getAuthorId()) {
            $idea->setAuthorId($this->_auth->getUser()->getId());
        }

        if ($register) {
            $this->coreRegistry->register('community_idea', $idea);
        }

        return $idea;
    }
}
