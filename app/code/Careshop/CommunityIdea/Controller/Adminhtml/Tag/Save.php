<?php

namespace Careshop\CommunityIdea\Controller\Adminhtml\Tag;

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
use Careshop\CommunityIdea\Controller\Adminhtml\Tag;
use Careshop\CommunityIdea\Model\TagFactory;
use Psr\Log\LoggerInterface;
use RuntimeException;

class Save extends Tag
{
    /**
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
     * @param TagFactory $tagFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Js $jsHelper,
        LayoutFactory $layoutFactory,
        JsonFactory $resultJsonFactory,
        TagFactory $tagFactory
    ) {
        $this->jsHelper = $jsHelper;
        $this->layoutFactory = $layoutFactory;
        $this->resultJsonFactory = $resultJsonFactory;

        parent::__construct($context, $registry, $tagFactory);
    }

    /**
     * @return $this|ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        if ($this->getRequest()->getPost('return_session_messages_only')) {
            $tag = $this->initTag();
            $tagPostData = $this->getRequest()->getPostValue();
            $tagPostData['store_ids'] = 0;
            $tagPostData['enabled'] = 1;

            $tag->addData($tagPostData);

            try {
                $tag->save();
                $this->messageManager->addSuccessMessage(__('You saved the tag.'));
            } catch (AlreadyExistsException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_objectManager->get(LoggerInterface::class)->critical($e);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_objectManager->get(LoggerInterface::class)->critical($e);
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while saving the tag.'));
                $this->_objectManager->get(LoggerInterface::class)->critical($e);
            }

            $hasError = (bool)$this->messageManager->getMessages()->getCountByType(
                MessageInterface::TYPE_ERROR
            );

            $tag->load($tag->getId());
            $tag->addData([
                'level' => 1,
                'entity_id' => $tag->getId(),
                'is_active' => $tag->getEnabled(),
                'parent' => 0
            ]);

            // to obtain truncated category name
            /** @var $block Messages */
            $block = $this->layoutFactory->create()->getMessagesBlock();
            $block->setMessages($this->messageManager->getMessages(true));

            /** @var Json $resultJson */
            $resultJson = $this->resultJsonFactory->create();

            return $resultJson->setData([
                'messages' => $block->getGroupedHtml(),
                'error' => $hasError,
                'category' => $tag->toArray(),
            ]);
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPost('tag')) {
            /** @var \Careshop\CommunityIdea\Model\Tag $tag */
            $tag = $this->initTag();

            $tag->addData($data);
            if ($ideas = $this->getRequest()->getPost('posts', false)) {
                $tag->setIdeasData($this->jsHelper->decodeGridSerializedInput($ideas));
            }

            $this->_eventManager->dispatch(
                'community_tag_prepare_save',
                ['tag' => $tag, 'request' => $this->getRequest()]
            );

            try {
                $tag->save();

                $this->messageManager->addSuccessMessage(__('The Tag has been saved.'));
                $this->_session->setData('community_tag_data', false);

                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('community/*/edit', ['id' => $tag->getId(), '_current' => true]);
                } else {
                    $resultRedirect->setPath('community/*/');
                }

                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Tag.'));
            }
            $this->_getSession()->setData('community_tag_data', $data);

            $resultRedirect->setPath('community/*/edit', ['id' => $tag->getId(), '_current' => true]);

            return $resultRedirect;
        }

        $resultRedirect->setPath('community/*/');

        return $resultRedirect;
    }
}
