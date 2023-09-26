<?php

namespace Careshop\CommunityIdea\Controller\Adminhtml\Topic;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Layout;
use Magento\Framework\View\Result\LayoutFactory;
use Careshop\CommunityIdea\Controller\Adminhtml\Topic;
use Careshop\CommunityIdea\Model\TopicFactory;

class Ideas extends Topic
{
    /**
     * Result layout factory
     *
     * @var LayoutFactory
     */
    public $resultLayoutFactory;

    /**
     * Ideas constructor.
     *
     * @param LayoutFactory $resultLayoutFactory
     * @param TopicFactory $ideaFactory
     * @param Registry $registry
     * @param Context $context
     */
    public function __construct(
        Context $context,
        Registry $registry,
        LayoutFactory $resultLayoutFactory,
        TopicFactory $ideaFactory
    ) {
        $this->resultLayoutFactory = $resultLayoutFactory;

        parent::__construct($context, $registry, $ideaFactory);
    }

    /**
     * @return Layout
     */
    public function execute()
    {
        $this->initTopic(true);

        return $this->resultLayoutFactory->create();
    }
}
