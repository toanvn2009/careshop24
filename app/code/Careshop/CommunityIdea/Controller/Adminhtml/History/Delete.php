<?php

namespace Careshop\CommunityIdea\Controller\Adminhtml\History;

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Careshop\CommunityIdea\Controller\Adminhtml\History;


class Delete extends History
{
    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $ideaId = null;
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $history = $this->ideaHistoryFactory->create()
                    ->load($id);
                $ideaId = $history->getIdeaId();
                $history->delete();

                $this->messageManager->addSuccessMessage(__('The Idea History has been deleted.'));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage(__('Idea History to delete was not found.'));
        }
        if ($ideaId) {
            $resultRedirect->setPath('community/idea/edit', ['id' => $ideaId]);
        } else {
            $resultRedirect->setPath('community/idea/index');
        }

        return $resultRedirect;
    }
}
