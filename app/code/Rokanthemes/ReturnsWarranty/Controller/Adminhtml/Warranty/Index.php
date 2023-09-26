<?php
 
namespace Rokanthemes\ReturnsWarranty\Controller\Adminhtml\Warranty;
 
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
 
class Index extends Action
{
    protected $_coreRegistry;
 
    protected $_resultPageFactory;
 
    
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory
    ) {
       parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
    }
 
   public function execute()
   {
      	if($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        $resultPage = $this->_resultPageFactory->create();
 
        return $resultPage;
   }
}
 