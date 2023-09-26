<?php

namespace Rokanthemes\Reviews\Controller\Customer;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;

class improvePost extends \Magento\Framework\App\Action\Action
{
	
	protected $resultPageFactory;
	protected $customerSession;
	protected $_improve;

	public function __construct(
		Context $context,
		\Magento\Customer\Model\Session $customerSession,
		\Rokanthemes\Reviews\Model\ImproveFactory $improve,
		PageFactory $resultPageFactory
	)
	{
		$this->resultPageFactory = $resultPageFactory;
		$this->customerSession = $customerSession;
		$this->_improve = $improve;
		parent::__construct($context);
	}
    /**
     * @return void
     */
    public function execute()
    {
		$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		$customer_id = $this->customerSession->getId();
        $data = $this->getRequest()->getPostValue();
		$id = $this->getRequest()->getParam('id');
        if (!empty($data)) {
            $improve = $this->_improve->create();
			$improve->setCustomerId($customer_id);
			$improve->setProductId($id);
			$improve->setProductIdKey($data['entity_simple_id']);
			$improve->setTitle($data['title']);
			$improve->setDetail($data['detail']);
			$improve->save();
			$success = __("Success.");
        }
		$this->messageManager->addSuccessMessage($success);
        return $this->_redirect('reviews/index/index');
	}
}
