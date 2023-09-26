<?php

namespace Careshop\CommunityIdea\Controller\Adminhtml\Idea;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\LayoutFactory;
use Careshop\CommunityIdea\Controller\Adminhtml\Idea;
use Careshop\CommunityIdea\Model\IdeaFactory;
use Careshop\CommunityIdea\Model\IdeaHistoryFactory;

class Products extends Idea
{
    /**
     * @var LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * @var IdeaHistoryFactory
     */
    protected $ideaHistoryFactory;

    /**
     * Products constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param IdeaFactory $productFactory
     * @param IdeaHistoryFactory $ideaHistoryFactory
     * @param LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        IdeaFactory $productFactory,
        IdeaHistoryFactory $ideaHistoryFactory,
        LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($productFactory, $registry, $context);

        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->ideaHistoryFactory = $ideaHistoryFactory;
    }

    /**
     * Save action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('history')) {
            $history = $this->ideaHistoryFactory->create()->load($this->getRequest()->getParam('id'));
            $this->coreRegistry->register('community_idea', $history);
        } else {
            $this->initIdea(true);
        }

        return $this->resultLayoutFactory->create();
    }
}
