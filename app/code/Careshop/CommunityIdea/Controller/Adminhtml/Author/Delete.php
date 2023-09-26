<?php

namespace Careshop\CommunityIdea\Controller\Adminhtml\Author;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Registry;
use Careshop\CommunityIdea\Controller\Adminhtml\Author;
use Careshop\CommunityIdea\Model\AuthorFactory;
use Careshop\CommunityIdea\Model\IdeaFactory;

class Delete extends Author
{
    /**
     * @var IdeaFactory
     */
    protected $_ideaFactory;

    /**
     * Delete constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param AuthorFactory $authorFactory
     * @param IdeaFactory $ideaFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        AuthorFactory $authorFactory,
        IdeaFactory $ideaFactory
    ) {
        $this->_ideaFactory = $ideaFactory;

        parent::__construct($context, $coreRegistry, $authorFactory);
    }

    /**
     * @return Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($id = $this->getRequest()->getParam('id')) {
            $idea = $this->_ideaFactory->create();
            $ideaCollectionSize = $idea->getCollection()->addFieldToFilter('author_id', ['eq' => $id])->getSize();
            if ($ideaCollectionSize > 0) {
                $this->messageManager->addErrorMessage(__('You can not delete this author.'
                    . ' This is the author of %1 post(s)', $ideaCollectionSize));
                $resultRedirect->setPath('community/*/edit', ['id' => $id]);

                return $resultRedirect;
            }
            try {
                $this->authorFactory->create()
                    ->load($id)
                    ->delete();

                $this->messageManager->addSuccessMessage(__('The Author has been deleted.'));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect->setPath('community/*/edit', ['id' => $id]);

                return $resultRedirect;
            }
        } else {
            $this->messageManager->addErrorMessage(__('Author to delete was not found.'));
        }

        $resultRedirect->setPath('community/*/');

        return $resultRedirect;
    }
}
