<?php

namespace Careshop\CommunityTesterProduct\Controller\Adminhtml\Tester;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Careshop\CommunityTesterProduct\Model\Tester;
use Careshop\CommunityTesterProduct\Model\TesterFactory;
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
    public $testerFactory;

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
        TesterFactory $testerFactory
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->testerFactory = $testerFactory;

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
        $testerItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && !empty($testerItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        $key = array_keys($testerItems);
        $testerId = !empty($key) ? (int)$key[0] : '';
        /** @var Idea $idea */
        $tester = $this->testerFactory->create()->load($testerId);
        try {
            $testerData = $testerItems[$testerId];
            $tester->addData($testerData);
            $tester->save();
        } catch (LocalizedException $e) {
            $messages[] = $this->getErrorWithIdeaId($tester, $e->getMessage());
            $error = true;
        } catch (RuntimeException $e) {
            $messages[] = $this->getErrorWithIdeaId($tester, $e->getMessage());
            $error = true;
        } catch (Exception $e) {
            $messages[] = $this->getErrorWithIdeaId(
                $tester,
                __('Something went wrong while saving the tester.')
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
    public function getErrorWithIdeaId(Tester $tester, $errorText)
    {
        return '[Tester ID: ' . $tester->getId() . '] ' . $errorText;
    }
}
