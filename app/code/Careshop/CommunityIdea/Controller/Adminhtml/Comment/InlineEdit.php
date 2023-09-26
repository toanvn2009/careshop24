<?php

namespace Careshop\CommunityIdea\Controller\Adminhtml\Comment;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Careshop\CommunityIdea\Model\Comment;
use Careshop\CommunityIdea\Model\CommentFactory;
use Careshop\CommunityIdea\Model\Idea;
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
     * Idea Factory
     *
     * @var CommentFactory
     */
    public $commentFactory;

    /**
     * InlineEdit constructor.
     *
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param CommentFactory $commentFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        CommentFactory $commentFactory
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->commentFactory = $commentFactory;

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
        $commentItems = $this->getRequest()->getParam('items', []);

        if (!($this->getRequest()->getParam('isAjax') && !empty($commentItems))) {
            return $resultJson->setData([
                'messages' => [
                    __('Please correct the data sent.')
                ],
                'error' => true,
            ]);
        }

        $key = array_keys($commentItems);
        $commentId = !empty($key) ? (int)$key[0] : '';

        /** @var Idea $idea */
        $comment = $this->commentFactory->create()->load($commentId);
        try {
            $commentData = $commentItems[$commentId];
            $comment->addData($commentData);
            $comment->save();
        } catch (LocalizedException $e) {
            $messages[] = $this->getErrorWithCommentId($comment, $e->getMessage());
            $error = true;
        } catch (RuntimeException $e) {
            $messages[] = $this->getErrorWithCommentId($comment, $e->getMessage());
            $error = true;
        } catch (Exception $e) {
            $messages[] = $this->getErrorWithCommentId(
                $comment,
                __('Something went wrong while saving the Comment.')
            );
            $error = true;
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * @param Comment $comment
     * @param $errorText
     *
     * @return string
     */
    public function getErrorWithCommentId(Comment $comment, $errorText)
    {
        return '[Comment ID: ' . $comment->getId() . '] ' . $errorText;
    }
}
