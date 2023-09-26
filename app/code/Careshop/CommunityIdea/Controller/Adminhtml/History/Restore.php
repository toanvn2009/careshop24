<?php

namespace Careshop\CommunityIdea\Controller\Adminhtml\History;

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Careshop\CommunityIdea\Controller\Adminhtml\History;
use Careshop\CommunityIdea\Helper\Data;
use Careshop\CommunityIdea\Model\IdeaHistory;

class Restore extends History
{
    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $ideaId = $this->getRequest()->getParam('idea_id');
        $historyId = $this->getRequest()->getParam('id');
        if ($historyId) {
            try {
                $history = $this->ideaHistoryFactory->create()
                    ->load($historyId);
                $idea = $this->ideaFactory->create()->load($ideaId);

                $data = $this->prepareData($history);

                $idea->addData($data)->save();

                $this->messageManager->addSuccessMessage(__('The Idea History has been restore.'));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage(__('Idea History to restore was not found.'));
        }

        $resultRedirect->setPath('community/idea/edit', ['id' => $ideaId]);

        return $resultRedirect;
    }

    /**
     * @param IdeaHistory $history
     *
     * @return array
     */
    protected function prepareData($history)
    {
        $history->setUpdatedAt($this->date->date());
        $history->setData('categories_ids', empty($history->getCategoryIds())
            ? [] : explode(',', $history->getCategoryIds()));
        $history->setData('tags_ids', empty($history->getTagIds()) ? [] : explode(',', $history->getTagIds()));
        $history->setData('topics_ids', empty($history->getTopicIds()) ? [] : explode(',', $history->getTopicIds()));
        $history->setData('products_data', empty($history->getProductIds())
            ? [] : Data::jsonDecode($history->getProductIds()));
        $data = $history->getData();
        unset(
            $data['idea_id'],
            $data['history_id'],
            $data['category_ids'],
            $data['tag_ids'],
            $data['topic_ids'],
            $data['product_ids']
        );

        return $data;
    }
}
