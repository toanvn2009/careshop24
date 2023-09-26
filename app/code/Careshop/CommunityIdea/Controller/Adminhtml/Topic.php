<?php

namespace Careshop\CommunityIdea\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Careshop\CommunityIdea\Model\TopicFactory;

abstract class Topic extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'Careshop_CommunityIdea::topic';

    /**
     * Topic Factory
     *
     * @var TopicFactory
     */
    public $topicFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * Topic constructor.
     *
     * @param TopicFactory $topicFactory
     * @param Registry $coreRegistry
     * @param Context $context
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        TopicFactory $topicFactory
    ) {
        $this->topicFactory = $topicFactory;
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context);
    }

    /**
     * @param bool $register
     *
     * @return bool|\Careshop\CommunityIdea\Model\Topic
     */
    protected function initTopic($register = false)
    {
        $topicId = (int)$this->getRequest()->getParam('id');

        /** @var \Careshop\CommunityIdea\Model\Topic $topic */
        $topic = $this->topicFactory->create();
        if ($topicId) {
            $topic->load($topicId);
            if (!$topic->getId()) {
                $this->messageManager->addErrorMessage(__('This topic no longer exists.'));

                return false;
            }
        }

        if ($register) {
            $this->coreRegistry->register('community_topic', $topic);
        }

        return $topic;
    }
}
