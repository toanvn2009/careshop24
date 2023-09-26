<?php

namespace Careshop\CommunityDevelopProduct\Controller\Adminhtml\Develop;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Careshop\CommunityDevelopProduct\Controller\Adminhtml\Develop;
use Careshop\CommunityDevelopProduct\Model\DevelopFactory;

class Edit extends \Careshop\CommunityDevelopProduct\Controller\Adminhtml\Develop
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
        DevelopFactory $developFactory,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($developFactory, $registry, $context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|Redirect|Page
     */
    public function execute()
    {
        /** @var \Careshop\CommunityDevelopProduct\Model\Develop $develop */
        $develop = $this->initDevelop();
        $duplicate = $this->getRequest()->getParam('duplicate');

        if (!$develop) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*');

            return $resultRedirect;
        }

        $data = $this->_session->getData('community_develop_data', true);
        if (!empty($data)) {
            $develop->setData($data);
        }

        $this->coreRegistry->register('community_develop', $develop);

        /** @var \Magento\Backend\Model\View\Result\Page|Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Careshop_CommunityDevelopProduct::develop');
        $resultPage->getConfig()->getTitle()->set(__('Develop'));

        $title = $develop->getId() && !$duplicate ? $develop->getName() : __('New develop');
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
