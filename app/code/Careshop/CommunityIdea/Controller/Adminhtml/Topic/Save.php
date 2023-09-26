<?php

namespace Careshop\CommunityIdea\Controller\Adminhtml\Topic;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Messages;
use Magento\Framework\View\LayoutFactory;
use Careshop\CommunityIdea\Controller\Adminhtml\Topic;
use Careshop\CommunityIdea\Model\TopicFactory;
use Psr\Log\LoggerInterface;
use RuntimeException;

class Save extends Topic
{
    /**
     * JS helper
     *
     * @var Js
     */
    public $jsHelper;

    /**
     * Layout Factory
     *
     * @var LayoutFactory
     */
    public $layoutFactory;

    /**
     * Result Json Factory
     *
     * @var JsonFactory
     */
    public $resultJsonFactory;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param Js $jsHelper
     * @param LayoutFactory $layoutFactory
     * @param JsonFactory $resultJsonFactory
     * @param TopicFactory $topicFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Js $jsHelper,
        LayoutFactory $layoutFactory,
        JsonFactory $resultJsonFactory,
        TopicFactory $topicFactory
    ) {
        $this->jsHelper = $jsHelper;
        $this->layoutFactory = $layoutFactory;
        $this->resultJsonFactory = $resultJsonFactory;

        parent::__construct($context, $registry, $topicFactory);
    }

    /**
     * @return $this|ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        if ($this->getRequest()->getPost('return_session_messages_only')) {
            $topic = $this->initTopic();
            $topicIdeaData = $this->getRequest()->getPostValue();
            $topicIdeaData['store_ids'] = 0;
            $topicIdeaData['enabled'] = 1;

            $topic->addData($topicIdeaData);

            try {
                $topic->save();
                $this->messageManager->addSuccessMessage(__('You saved the topic.'));
            } catch (AlreadyExistsException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_objectManager->get(LoggerInterface::class)->critical($e);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_objectManager->get(LoggerInterface::class)->critical($e);
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while saving the topic.'));
                $this->_objectManager->get(LoggerInterface::class)->critical($e);
            }

            $hasError = (bool)$this->messageManager->getMessages()->getCountByType(
                MessageInterface::TYPE_ERROR
            );

            $topic->load($topic->getId());
            $topic->addData([
                'level' => 1,
                'entity_id' => $topic->getId(),
                'is_active' => $topic->getEnabled(),
                'parent' => 0
            ]);

            // to obtain truncated category name
            /** @var $block Messages */
            $block = $this->layoutFactory->create()->getMessagesBlock();
            $block->setMessages($this->messageManager->getMessages(true));

            /** @var Json $resultJson */
            $resultJson = $this->resultJsonFactory->create();

            return $resultJson->setData(
                [
                    'messages' => $block->getGroupedHtml(),
                    'error' => $hasError,
                    'category' => $topic->toArray(),
                ]
            );
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPost('topic')) {
            /** @var \Careshop\CommunityIdea\Model\Topic $topic */
            $topic = $this->initTopic();
            $topic->setData($data);
          
            if ($ideas = $this->getRequest()->getPost('ideas', false)) {
                $topic->setIdeasData($this->jsHelper->decodeGridSerializedInput($ideas));
            }
            echo "<pre>"; print_r($topic->getData()); die('ssddfs');
            try {
                $topic->save();

                $this->messageManager->addSuccessMessage(__('The Topic has been saved.'));
                $this->_getSession()->setData('community_topic_data', false);

                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('community/*/edit', ['id' => $topic->getId(), '_current' => true]);
                } else {
                    $resultRedirect->setPath('community/*/');
                }

                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Topic.'));
            }

            $this->_getSession()->setData('community_topic_data', $data);

            $resultRedirect->setPath('community/*/edit', ['id' => $topic->getId(), '_current' => true]);

            return $resultRedirect;
        }

        $resultRedirect->setPath('community/*/');

        return $resultRedirect;
    }
}
