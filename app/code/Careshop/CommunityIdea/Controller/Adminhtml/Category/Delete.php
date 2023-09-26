<?php

namespace Careshop\CommunityIdea\Controller\Adminhtml\Category;

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Careshop\CommunityIdea\Controller\Adminhtml\Category;


class Delete extends Category
{
    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $categoryFactory = $this->categoryFactory->create();
                if ($id !== 1) {
                    $parentCategoryCollection = $categoryFactory->getCollection()
                        ->addFieldToFilter('category_id', ['eq' => $id])
                        ->addFieldToFilter('parent_id', ['eq' => 1]);
                    if ($parentCategoryCollection->getSize()) {
                        $pathCategory = $categoryFactory->load($id)->getPath();
                        $collections  = $categoryFactory->getCollection()
                            ->addFieldToFilter('path', ['like' => $pathCategory . '%']);
                        foreach ($collections as $collection) {
                            $collection->delete();
                        }
                    } else {
                        $categoryFactory->load($id)->delete();
                    }

                    $this->messageManager->addSuccessMessage(__('The Community Category has been deleted.'));

                    $resultRedirect->setPath('community/*/');

                    return $resultRedirect;
                }
            } catch (Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                $resultRedirect->setPath('community/*/edit', ['id' => $id]);

                return $resultRedirect;
            }
        }

        // display error message
        $this->messageManager->addErrorMessage(__('Community Category to delete was not found.'));
        // go to grid
        $resultRedirect->setPath('community/*/');

        return $resultRedirect;
    }
}
