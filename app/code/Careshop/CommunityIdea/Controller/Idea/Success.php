<?php

namespace Careshop\CommunityIdea\Controller\Idea;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Careshop\CommunityIdea\Model\IdeaFactory;

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
        PageFactory $resultPageFactory,
        IdeaFactory $ideaFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->ideaFactory = $ideaFactory;
        parent::__construct($context);
    }

    /**
     * @return Page
     */
    public function execute()
    {  

        $page = $this->resultPageFactory->create();
        $page->getConfig()->setPageLayout('1column');
        $id = $this->getRequest()->getParam('id');
        $idea = $this->ideaFactory->create()->load($id);

        $page->getConfig()->getTitle()->set(__('Posted'));
        $breadcrumbs = $page->getLayout()->getBlock('breadcrumbs');
		$breadcrumbs->addCrumb('Community Overview', [
			'label' => __('Community Overview'),
			'title' => __('Community Overview'),
			'link' => $this->_url->getUrl('community')
				]
		);
		$breadcrumbs->addCrumb('idea_list', [
			'label' => __('Share Product Ideas'),
			'title' => __('Share Product Ideas'),
            'link' => $this->_url->getUrl('community/idea')
				]
		);

        $breadcrumbs->addCrumb('idea_reply', [
			'label' => $idea->getName(),
			'title' => $idea->getName(),
            'link' => $this->_url->getUrl('community/idea/reply',array('id'=>$id))
				]
		);

        $breadcrumbs->addCrumb('success', [
			'label' => __('Posted'),
			'title' => __('Posted'),
				]
		);


        return $page;
    }
}
