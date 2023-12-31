<?php

namespace Careshop\CommunityIdea\Controller\Adminhtml\Topic;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Careshop\CommunityIdea\Model\Topic;
use Careshop\CommunityIdea\Model\TopicFactory;
use RuntimeException;

class InlineEdit extends Action
{
    /**
     * JSON Factory
     *
     * @var JsonFactory
     */
    public $jsonFactory;

    /**
     * Topic Factory
     *
     * @var TopicFactory
     */
    public $topicFactory;

    /**
     * InlineEdit constructor.
     *
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param TopicFactory $topicFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        TopicFactory $topicFactory
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->topicFactory = $topicFactory;

        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        $ideaItems = $this->getRequest()->getParam('items', []);

        if (!($this->getRequest()->getParam('isAjax') && !empty($ideaItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        $key = array_keys($ideaItems);
        $topicId = !empty($key) ? (int)$key[0] : '';
        /** @var Topic $topic */
        $topic = $this->topicFactory->create()->load($topicId);
        try {
            $topic->addData($ideaItems[$topicId])
                ->save();
        } catch (LocalizedException $e) {
            $messages[] = $this->getErrorWithTopicId($topic, $e->getMessage());
            $error = true;
        } catch (RuntimeException $e) {
            $messages[] = $this->getErrorWithTopicId($topic, $e->getMessage());
            $error = true;
        } catch (Exception $e) {
            $messages[] = $this->getErrorWithTopicId(
                $topic,
                __('Something went wrong while saving the Topic.')
            );
            $error = true;
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add Topic id to error message
     *
     * @param Topic $topic
     * @param string $errorText
     *
     * @return string
     */
    public function getErrorWithTopicId(Topic $topic, $errorText)
    {
        return '[Topic ID: ' . $topic->getId() . '] ' . $errorText;
    }
}
