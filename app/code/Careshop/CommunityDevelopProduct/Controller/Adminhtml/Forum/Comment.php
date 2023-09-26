<?php

namespace Careshop\CommunityDevelopProduct\Controller\Adminhtml\Forum;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\LayoutFactory;
use Careshop\CommunityDevelopProduct\Controller\Adminhtml\Forum;
use Careshop\CommunityDevelopProduct\Model\ForumFactory;

class Comment extends Forum
{
    /**
     * @var LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * Products constructor.
     *
     * @param Context $context
     * @param IdeaFactory $productFactory
     * @param Registry $registry
     * @param LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ForumFactory $productFactory,
        LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($productFactory, $registry, $context);

        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    /**
     * Save action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $this->initForum(true);
        return $this->resultLayoutFactory->create();
    }
}
