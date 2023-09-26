<?php

namespace Careshop\CommunityDevelopProduct\Controller\Adminhtml\Comment;

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Careshop\CommunityDevelopProduct\Controller\Adminhtml\Comment;


class Delete extends \Careshop\CommunityDevelopProduct\Controller\Adminhtml\Comment
{
    /** 
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $forumId = null;
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $comment = $this->commentForumFactory->create()
                    ->load($id);
                $forumId = $comment->getForumId();
                $comment->delete();

                $this->messageManager->addSuccessMessage(__('The Forum comment has been deleted.'));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage(__('Forum comment to delete was not found.'));
        }
        if ($forumId) {
            $resultRedirect->setPath('communitydevelop/forum/edit', ['id' => $forumId]);
        } else {
            $resultRedirect->setPath('communitydevelop/forum/index');
        }

        return $resultRedirect;
    }
}
