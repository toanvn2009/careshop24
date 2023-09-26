<?php

namespace Careshop\CommunityTesterProduct\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Careshop\CommunityTesterProduct\Model\TesterFactory;

abstract class Tester extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'Careshop_CommunityTesterProduct::tester';

    /**
     * Idea Factory
     *
     * @var TesterFactory
     */
    public $testerFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * Idea constructor.
     *
     * @param TesterFactory $developFactory
     * @param Registry $coreRegistry
     * @param Context $context
     */
    public function __construct(
        TesterFactory $testerFactory,
        Registry $coreRegistry,
        Context $context
    ) {
        $this->testerFactory = $testerFactory;
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context);
    }

    /**
     * @param bool $register
     * @param bool $isSave
     *
     * @return bool|\Careshop\CommunityIdea\Model\Idea
     */
    protected function initTester($register = false, $isSave = false)
    {
        $testerId = (int)$this->getRequest()->getParam('id');

        /** @var \Careshop\CommunityIdea\Model\Idea $idea */
        $tester = $this->testerFactory->create();
        if ($testerId) {
            if (!$isSave) {
                $tester->load($testerId);
                if (!$tester->getId()) {
                    $this->messageManager->addErrorMessage(__('This post no longer exists.'));

                    return false;
                }
            }
        }

        if ($register) {
            $this->coreRegistry->register('community_tester', $tester);
        }

        return $tester;
    }
}
