<?php

namespace Careshop\CommunityIdea\Controller\Adminhtml\Author;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Careshop\CommunityIdea\Model\Author;
use Careshop\CommunityIdea\Model\AuthorFactory;
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
     * Author Factory
     *
     * @var AuthorFactory
     */
    public $authorFactory;

    /**
     * InlineEdit constructor.
     *
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param AuthorFactory $ideaFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        AuthorFactory $ideaFactory
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->authorFactory = $ideaFactory;

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
        $authorItems = $this->getRequest()->getParam('items', []);
        if (!(!empty($authorItems) && $this->getRequest()->getParam('isAjax'))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        $key = array_keys($authorItems);
        $authorId = !empty($key) ? (int)$key[0] : '';
        /** @var Idea $idea */
        $author = $this->authorFactory->create()->load($authorId);
        try {
            $authorData = $authorItems[$authorId];
            $author->addData($authorData);
            $author->save();
        } catch (LocalizedException $e) {
            $messages[] = $this->getErrorWithIdeaId($author, $e->getMessage());
            $error = true;
        } catch (RuntimeException $e) {
            $messages[] = $this->getErrorWithIdeaId($author, $e->getMessage());
            $error = true;
        } catch (Exception $e) {
            $messages[] = $this->getErrorWithIdeaId(
                $author,
                __('Something went wrong while saving the Idea.')
            );
            $error = true;
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * @param Author $author
     * @param $errorText
     *
     * @return string
     */
    public function getErrorWithIdeaId(Author $author, $errorText)
    {
        return '[Author ID: ' . $author->getId() . '] ' . $errorText;
    }
}
