<?php

namespace Careshop\CommunityTesterProduct\Controller\Adminhtml\Tester;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Careshop\CommunityTesterProduct\Controller\Adminhtml\Tester;
use Careshop\CommunityTesterProduct\Model\TesterFactory;

class Edit extends \Careshop\CommunityTesterProduct\Controller\Adminhtml\Tester
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
        TesterFactory $testerFactory,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($testerFactory, $registry, $context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|Redirect|Page
     */
    public function execute()
    {
        /** @var \Careshop\CommunityDevelopProduct\Model\Develop $develop */
        $tester = $this->initTester();
        $duplicate = $this->getRequest()->getParam('duplicate');

        if (!$tester) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*');

            return $resultRedirect;
        }

        $data = $this->_session->getData('community_tester_data', true);
        if (!empty($data)) {
            $tester->setData($data);
        }

        $this->coreRegistry->register('community_tester', $tester);

        /** @var \Magento\Backend\Model\View\Result\Page|Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Careshop_CommunityTesterProduct::tester');
        $resultPage->getConfig()->getTitle()->set(__('Tester'));

        $title = $tester->getId() && !$duplicate ? $tester->getName() : __('New tester');
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
