<?php

namespace Careshop\CommunityIdea\Controller\Adminhtml\Author;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Careshop\CommunityIdea\Model\ResourceModel\Author\CollectionFactory;

class MassDelete extends Action
{
    /**
     * Mass Action Filter
     *
     * @var Filter
     */
    public $filter;

    /**
     * Collection Factory
     *
     * @var CollectionFactory
     */
    public $collectionFactory;

    /**
     * constructor
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;

        parent::__construct($context);
    }

    /**
     * @return Redirect|ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $authorRemove = 0;

        foreach ($collection->getItems() as $author) {
            if ($author->hasPost()) {
                $this->messageManager->addWarningMessage(__('One of the authors has idea. You can not delete this one.'));
            } else {
                try {
                    $author->delete();
                    $authorRemove++;
                } catch (Exception $e) {
                    $this->_getSession()->addException(
                        $e,
                        __('Something went wrong while updating status for %1.', $author->getName())
                    );
                }
            }
        }

        if ($authorRemove) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been removed.', $authorRemove));
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
