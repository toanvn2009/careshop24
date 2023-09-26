<?php

namespace Careshop\CommunityDevelopProduct\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Careshop\CommunityDevelopProduct\Model\DevelopFactory;

abstract class Develop extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'Careshop_CommunityDevelopProduct::develop';

    /**
     * Idea Factory
     *
     * @var DevelopFactory
     */
    public $developFactory;

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
        DevelopFactory $developFactory,
        Registry $coreRegistry,
        Context $context
    ) {
        $this->developFactory = $developFactory;
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context);
    }

    /**
     * @param bool $register
     * @param bool $isSave
     *
     * @return bool|\Careshop\CommunityIdea\Model\Idea
     */
    protected function initDevelop($register = false, $isSave = false)
    {
        $developId = (int)$this->getRequest()->getParam('id');
        $duplicate = $this->getRequest()->getParam('post')['duplicate'] ?? null;

        /** @var \Careshop\CommunityIdea\Model\Idea $idea */
        $develop = $this->developFactory->create();
        if ($developId) {
            if (!$isSave || !$duplicate) {
                $develop->load($developId);
                if (!$develop->getId()) {
                    $this->messageManager->addErrorMessage(__('This post no longer exists.'));

                    return false;
                }
            }
        }

        if (!$develop->getAuthorId()) {
            $develop->setAuthorId($this->_auth->getUser()->getId());
        }

        if ($register) {
            $this->coreRegistry->register('community_develop', $develop);
        }

        return $develop;
    }
}
