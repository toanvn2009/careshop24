<?php

namespace Careshop\CommunityDevelopProduct\Controller\Adminhtml\Develop;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Careshop\CommunityDevelopProduct\Model\DevelopFactory;
use Careshop\CommunityTesterProduct\Model\TesterFactory;

class ApplyTest extends Action
{
    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;
    protected $developFactory;
    protected $_productloader;

    /**
     * Idea constructor.
     *
     * @param IdeaFactory $ideaFactory
     * @param Registry $coreRegistry
     * @param Context $context
     */
    public function __construct(
        Registry $coreRegistry,
        DevelopFactory $developFactory,
        TesterFactory $testerFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagementInterface,
        Context $context
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->developFactory = $developFactory;
        $this->_resource = $resource;
        $this->testerFactory = $testerFactory;
        $this->_productloader = $_productloader;
        $this->categoryLinkManagement = $categoryLinkManagementInterface;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {  
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $connection = $this->_resource->getConnection();
            $tableName = $this->_resource->getTableName('community_tester');
            $id = $this->getRequest()->getParam('id');
            $develop = $this->developFactory->create();
            $tester = $this->testerFactory->create();
            $develop = $develop->load($id);
            $product_id = $develop->getProductId();
            $_product = $this->_productloader->create()->load($product_id);
            $categoryIds = [
                13,
                15
            ];
            $this->categoryLinkManagement->assignProductToCategories(
                $_product->getSku(),
                $categoryIds
            );
            $sql = "Insert Into " . $tableName . " (name, idea_id, customer_id, product_id) Values ('".$develop->getName()."',".$develop->getIdeaId().",".$develop->getCustomerId().",".$develop->getProductId().")";
            $connection->query($sql);
            $develop->delete(); 
            $this->messageManager->addSuccessMessage(__('The product develop have to apply to tester product successfull.'));
        } catch (RuntimeException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('The missing while save product.')
            );
        }
        $resultRedirect->setPath('communitydevelop/develop/index');
        return $resultRedirect;
    }
}
