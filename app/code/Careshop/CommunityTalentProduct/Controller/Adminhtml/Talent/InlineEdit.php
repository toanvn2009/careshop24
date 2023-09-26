<?php

namespace Careshop\CommunityTalentProduct\Controller\Adminhtml\Talent;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Careshop\CommunityTalentProduct\Model\Talent;
use Careshop\CommunityTalentProduct\Model\TalentFactory;
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
     * Talent Factory
     *
     * @var TalentFactory
     */
    public $talentFactory;

    /**
     * InlineEdit constructor.
     *
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param TalentFactory $ideaFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        TalentFactory $talentFactory
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->talentFactory = $talentFactory;
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
        $talentItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && !empty($talentItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        $key = array_keys($talentItems);
        $talentId = !empty($key) ? (int)$key[0] : '';
        /** @var Idea $idea */
        $talent = $this->talentFactory->create()->load($talentId);
        try {
            $talentData = $talentItems[$talentId];
            $talent->addData($talentData);
            $talent->save();
        } catch (LocalizedException $e) {
            $messages[] = $this->getErrorWithIdeaId($talent, $e->getMessage());
            $error = true;
        } catch (RuntimeException $e) {
            $messages[] = $this->getErrorWithIdeaId($talent, $e->getMessage());
            $error = true;
        } catch (Exception $e) {
            $messages[] = $this->getErrorWithIdeaId(
                $talent,
                __('Something went wrong while saving the talent.')
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
    public function getErrorWithIdeaId(Talent $talent, $errorText)
    {
        return '[Talent ID: ' . $talent->getId() . '] ' . $errorText;
    }
}
