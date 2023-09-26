<?php
namespace Careshop\CommunityDevelopProduct\Controller\Adminhtml\Forum;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Careshop\CommunityDevelopProduct\Controller\Adminhtml\Forum;
use Careshop\CommunityDevelopProduct\Model\ForumFactory;

class Edit extends \Careshop\CommunityDevelopProduct\Controller\Adminhtml\Forum
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
        ForumFactory $forumFactory,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($forumFactory, $registry, $context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|Redirect|Page
     */
    public function execute()
    {
        /** @var \Careshop\CommunityDevelopProduct\Model\Develop $develop */
        $forum = $this->initForum();

        if (!$forum) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*');
            return $resultRedirect;
        }

        $data = $this->_session->getData('community_develop_forum_data', true);
        if (!empty($data)) {
            $forum->setData($data);
        }

        $this->coreRegistry->register('community_develop_forum', $forum);

        /** @var \Magento\Backend\Model\View\Result\Page|Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Careshop_CommunityDevelopProduct::develop_forum');
        $resultPage->getConfig()->getTitle()->set(__('Forum'));

        $title = $forum->getId() ? $forum->getName() : __('Forum');
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
