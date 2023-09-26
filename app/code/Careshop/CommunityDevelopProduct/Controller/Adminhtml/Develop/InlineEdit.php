<?php

namespace Careshop\CommunityDevelopProduct\Controller\Adminhtml\Develop;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Careshop\CommunityDevelopProduct\Model\Develop;
use Careshop\CommunityDevelopProduct\Model\DevelopFactory;
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
     * Develop Factory
     *
     * @var DevelopFactory
     */
    public $developFactory;

    /**
     * InlineEdit constructor.
     *
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param IdeaFactory $ideaFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        DevelopFactory $developFactory
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->developFactory = $developFactory;

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
        $developItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && !empty($developItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        $key = array_keys($developItems);
        $developId = !empty($key) ? (int)$key[0] : '';
        /** @var Idea $idea */
        $develop = $this->developFactory->create()->load($developId);
        try {
            $developData = $developItems[$developId];
            $develop->addData($developData);
            $develop->save();
        } catch (LocalizedException $e) {
            $messages[] = $this->getErrorWithIdeaId($develop, $e->getMessage());
            $error = true;
        } catch (RuntimeException $e) {
            $messages[] = $this->getErrorWithIdeaId($develop, $e->getMessage());
            $error = true;
        } catch (Exception $e) {
            $messages[] = $this->getErrorWithIdeaId(
                $develop,
                __('Something went wrong while saving the develop.')
            );
            $error = true;
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add Idea id to error message
     *
     * @param Idea $idea
     * @param string $errorText
     *
     * @return string
     */
    public function getErrorWithIdeaId(Idea $idea, $errorText)
    {
        return '[Idea ID: ' . $idea->getId() . '] ' . $errorText;
    }
}
