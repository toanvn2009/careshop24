<?php

namespace Careshop\CommunityDevelopProduct\Controller\Forum;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Success extends Action
{
    /**
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * @var IdeaFactory
     */
    protected $ideaFactory;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return Page
     */
    public function execute()
    {  

        $page = $this->resultPageFactory->create();
        $page->getConfig()->setPageLayout('1column');
        $page->getConfig()->getTitle()->prepend(__('Reply Forum Success'));
        return $page;
    }
}
