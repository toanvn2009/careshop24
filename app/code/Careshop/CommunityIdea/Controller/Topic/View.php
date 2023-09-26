<?php

namespace Careshop\CommunityIdea\Controller\Topic;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Careshop\CommunityIdea\Helper\Data as HelperCommunity;

/**
 * Class View
 * @package Careshop\CommunityIdea\Controller\Topic
 */
class View extends Action
{
    /**
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var HelperCommunity
     */
    public $helperCommunity;

    /**
     * View constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param HelperCommunity $helperCommunity
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        HelperCommunity $helperCommunity
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->helperCommunity = $helperCommunity;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $topic = $this->helperCommunity->getFactoryByType(HelperCommunity::TYPE_TOPIC)->create()->load($id);
        $page = $this->resultPageFactory->create();
        $page->getConfig()->setPageLayout($this->helperCommunity->getSidebarLayout());

        return $topic->getEnabled() ? $page : $this->_redirect('noroute');
    }
}
