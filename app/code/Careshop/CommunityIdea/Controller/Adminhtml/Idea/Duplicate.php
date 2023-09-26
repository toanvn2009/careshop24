<?php

namespace Careshop\CommunityIdea\Controller\Adminhtml\Idea;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Forward;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Careshop\CommunityIdea\Controller\Adminhtml\Idea;
use Careshop\CommunityIdea\Model\IdeaFactory;

class Duplicate extends Idea
{
    /**
     * Redirect result factory
     *
     * @var ForwardFactory
     */
    public $resultForwardFactory;

    /**
     * Duplicate constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param IdeaFactory $ideaFactory
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        IdeaFactory $ideaFactory,
        ForwardFactory $resultForwardFactory
    ) {
        $this->resultForwardFactory = $resultForwardFactory;

        parent::__construct($ideaFactory, $registry, $context);
    }

    /**
     * @return Forward|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        $resultForward->forward('edit');

        return $resultForward;
    }
}
