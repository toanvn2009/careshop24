<?php

namespace Careshop\CommunityTalentProduct\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Careshop\CommunityTalentProduct\Model\TalentFactory;

abstract class Talent extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'Careshop_CommunityTalentProduct::talent';

    /**
     * Talent Factory
     *
     * @var TalentFactory
     */
    public $talentFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * Idea constructor.
     *
     * @param TalentFactory $developFactory
     * @param Registry $coreRegistry
     * @param Context $context
     */
    public function __construct(
        TalentFactory $talentFactory,
        Registry $coreRegistry,
        Context $context
    ) {
        $this->talentFactory = $talentFactory;
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context);
    }

    /**
     * @param bool $register
     * @param bool $isSave
     *
     * @return bool|\Careshop\CommunityTalentProduct\Model\Idea
     */
    protected function initTalent($register = false, $isSave = false)
    {
        $talentId = (int)$this->getRequest()->getParam('id');

        /** @var \Careshop\CommunityTalentProduct\Model\Talent $idea */
        $talent = $this->talentFactory->create();
        if ($talentId) {
            if (!$isSave) {
                $talent->load($talentId);
                if (!$talent->getId()) {
                    $this->messageManager->addErrorMessage(__('This post no longer exists.'));

                    return false;
                }
            }
        }

        if ($register) {
            $this->coreRegistry->register('community_talent', $talent);
        }

        return $talent;
    }
}
