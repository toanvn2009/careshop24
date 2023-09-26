<?php

namespace Careshop\CommunityIdea\Controller\Adminhtml\Category;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Backend\Model\View\Result\Page;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Careshop\CommunityIdea\Controller\Adminhtml\Category;
use Careshop\CommunityIdea\Model\CategoryFactory;

class Edit extends Category
{
    /**
     * Page factory
     *
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * Result JSON factory
     *
     * @var JsonFactory
     */
    public $resultJsonFactory;

    /**
     * @var DataObject
     */
    public $dataObject;

    /**
     * Edit constructor.
     *
     * @param DataObject $dataObject
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     * @param CategoryFactory $categoryFactory
     * @param Registry $registry
     * @param Context $context
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CategoryFactory $categoryFactory,
        DataObject $dataObject,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory
    ) {
        $this->dataObject        = $dataObject;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;

        parent::__construct($context, $registry, $categoryFactory);
    }

    /**
     * Edit Community category page
     *
     * @return ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $categoryId = null;
        if($this->getRequest()->getParam('id')) {
            $categoryId = (int) $this->getRequest()->getParam('id');
        }
        $duplicate  = $this->getRequest()->getParam('duplicate');
        $category   = $this->initCategory();
        if ($category && $duplicate) {
            $category->setId(null);
            $category->setData('duplicate', true);
            $categoryId = null;
        }
        if (!$category) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*');

          //  return $resultRedirect;
        }

        /**
         * Check if we have data in session (if during Community category save was exception)
         */
        $data = $this->_getSession()->getData('community_category_data', true);
        
        if (isset($data['category'])) {
            $category->addData($data['category']);
        }
      
        if($category->getData()) {
            $this->coreRegistry->register('category', $category);
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        /** Build response for ajax request */
        if ($this->getRequest()->getQuery('isAjax')) {
            // prepare breadcrumbs of selected Community category, if any
            $breadcrumbsPath = $category->getPath();
            if (empty($breadcrumbsPath)) {
                // but if no Community category, and it is deleted - prepare breadcrumbs from path, saved in session
                $breadcrumbsPath = $this->_objectManager->get(Session::class)
                    ->getDeletedPath(true);
                if (!empty($breadcrumbsPath)) {
                    $breadcrumbsPath = explode('/', $breadcrumbsPath);
                    // no need to get parent breadcrumbs if deleting Community category level 1
                    if (count($breadcrumbsPath) <= 1) {
                        $breadcrumbsPath = '';
                    } else {
                        array_pop($breadcrumbsPath);
                        $breadcrumbsPath = implode('/', $breadcrumbsPath);
                    }
                }
            }

            $layout        = $resultPage->getLayout();
            $content       = $layout->getBlock('community.category.edit')->getFormHtml()
                . $layout->getBlock('community.category.tree')
                    ->getBreadcrumbsJavascript($breadcrumbsPath, 'editingCategoryBreadcrumbs');
            $eventResponse = $this->dataObject->addData([
                'content'  => $content,
                'messages' => $layout->getMessagesBlock()->getGroupedHtml(),
                'toolbar'  => $layout->getBlock('page.actions.toolbar')->toHtml()
            ]);

            $this->_eventManager->dispatch(
                'community_category_prepare_ajax_response',
                ['response' => $eventResponse, 'controller' => $this]
            );

            /** @var Json $resultJson */
            $resultJson = $this->resultJsonFactory->create();
            $resultJson->setHeader('Content-type', 'application/json', true);
            $resultJson->setData($eventResponse->getData());

            return $resultJson;
        }

        $resultPage->setActiveMenu('Careshop_CommunityIdea::category');
        $resultPage->getConfig()->getTitle()->prepend(__('Categories'));

        if ($categoryId) {
            $title = __('%1 (ID: %2)', $category->getName(), $categoryId);
        } else {
            $parentId = (int) $this->getRequest()->getParam('parent');
            if ($parentId && $parentId != CategoryModel::TREE_ROOT_ID) {
                $title = __('New Child Category');
            } else {
                $title = __('New Root Category');
            }
        }
        $resultPage->getConfig()->getTitle()->prepend($title);

        $resultPage->addBreadcrumb(__('Manage Categories'), __('Manage Categories'));

        return $resultPage;
    }
}
