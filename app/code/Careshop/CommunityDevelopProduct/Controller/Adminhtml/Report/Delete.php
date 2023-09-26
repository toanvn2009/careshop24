<?php

namespace Careshop\CommunityDevelopProduct\Controller\Adminhtml\Report;

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;

class Delete extends \Careshop\CommunityDevelopProduct\Controller\Adminhtml\Report
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
                $report = $this->reportForumFactory->create()
                    ->load($id);
                $forumId = $report->getForumId();
                $report->delete();

                $this->messageManager->addSuccessMessage(__('The Forum report has been deleted.'));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage(__('Forum report to delete was not found.'));
        }
        if ($forumId) {
            $resultRedirect->setPath('communitydevelop/forum/edit', ['id' => $forumId]);
        } else {
            $resultRedirect->setPath('communitydevelop/forum/index');
        }

        return $resultRedirect;
    }
}
