<?php

namespace Careshop\CommunityIdea\Controller\Adminhtml\Category;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutFactory;
use Careshop\CommunityIdea\Block\Adminhtml\Category\Tree;
use Careshop\CommunityIdea\Controller\Adminhtml\Category;
use Careshop\CommunityIdea\Model\CategoryFactory;

class SuggestCategories extends Category
{
    /**
     * Json result factory
     *
     * @var JsonFactory
     */
    public $resultJsonFactory;

    /**
     * Layout factory
     *
     * @var LayoutFactory
     */
    public $layoutFactory;

    /**
     * SuggestCategories constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param CategoryFactory $categoryFactory
     * @param JsonFactory $resultJsonFactory
     * @param LayoutFactory $layoutFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        CategoryFactory $categoryFactory,
        JsonFactory $resultJsonFactory,
        LayoutFactory $layoutFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory     = $layoutFactory;

        parent::__construct($context, $coreRegistry, $categoryFactory);
    }

    /**
     * Community Category list suggestion based on already entered symbols
     *
     * @return Json
     */
    public function execute()
    {
        /** @var Tree $treeBlock */
        $treeBlock = $this->layoutFactory->create()->createBlock(Tree::class);
        $data      = $treeBlock->getSuggestedCategoriesJson($this->getRequest()->getParam('label_part'));

        /** @var Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setJsonData($data);

        return $resultJson;
    }
}
