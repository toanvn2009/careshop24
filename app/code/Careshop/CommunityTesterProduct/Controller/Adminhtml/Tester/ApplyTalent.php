<?php

namespace Careshop\CommunityTesterProduct\Controller\Adminhtml\Tester;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Careshop\CommunityTesterProduct\Model\TesterFactory;

class ApplyTalent extends Action
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
        TesterFactory $testerFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagementInterface,
        Context $context
    ) {
        $this->coreRegistry = $coreRegistry;
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
            $tableName = $this->_resource->getTableName('community_talent');
            $id = $this->getRequest()->getParam('id');
            $tester = $this->testerFactory->create();
            $tester = $tester->load($id);
            $product_id = $tester->getProductId();
            $_product = $this->_productloader->create()->load($product_id);
            $categoryIds = [
                13,
                16
            ];
            $this->categoryLinkManagement->assignProductToCategories(
                $_product->getSku(),
                $categoryIds
            );
            $sql = "Insert Into " . $tableName . " (name, idea_id, customer_id, product_id) Values ('".$tester->getName()."',".$tester->getIdeaId().",".$tester->getCustomerId().",".$tester->getProductId().")";
            $connection->query($sql);
            $tester->delete(); 
            $this->messageManager->addSuccessMessage(__('The product tester have to apply to talent product successfull.'));
        } catch (RuntimeException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('The missing while save product.')
            );
        }
        $resultRedirect->setPath('communitytester/tester/index');
        return $resultRedirect;
    }
}
