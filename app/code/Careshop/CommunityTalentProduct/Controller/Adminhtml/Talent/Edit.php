<?php

namespace Careshop\CommunityTalentProduct\Controller\Adminhtml\Talent;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Careshop\CommunityTalentProduct\Controller\Adminhtml\Talent;
use Careshop\CommunityTalentProduct\Model\TalentFactory;

class Edit extends \Careshop\CommunityTalentProduct\Controller\Adminhtml\Talent
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
     * @param DevelopFactory $developFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        TalentFactory $talentFactory,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($talentFactory, $registry, $context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|Redirect|Page
     */
    public function execute()
    {
        /** @var \Careshop\CommunityDevelopProduct\Model\Develop $develop */
        $talent = $this->initTalent();
        $duplicate = $this->getRequest()->getParam('duplicate');

        if (!$talent) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*');

            return $resultRedirect;
        }

        $data = $this->_session->getData('community_talent_data', true);
        if (!empty($data)) {
            $talent->setData($data);
        }

        $this->coreRegistry->register('community_talent', $talent);

        /** @var \Magento\Backend\Model\View\Result\Page|Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Careshop_CommunityTalentProduct::talent');
        $resultPage->getConfig()->getTitle()->set(__('Talent'));

        $title = $talent->getId() && !$duplicate ? $talent->getName() : __('New talent');
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
