<?php

namespace Careshop\CommunityDevelopProduct\Controller\Forum;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Careshop\CommunityDevelopProduct\Model\DevelopFactory;

class CountView extends Action
{
    protected $developFactory;

    protected $_pageFactory;

    protected $storeManager;

    private $json;
    private $resultJsonFactory;

    public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        DevelopFactory $developFactory
        )
	{
		$this->_pageFactory = $pageFactory;
        $this->developFactory = $developFactory;
        $this->storeManager = $storeManager;
        $this->json = $json;
        $this->resultJsonFactory = $resultJsonFactory;
		return parent::__construct($context);
	}

    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface|Page
     */
    public function execute()
    {   
        $data = $this->getRequest()->getPost();
        $params = $this->getRequest()->getParams(); 
        if( $data['develop_id']) {
            $forum = $this->developFactory->create()->load($data['develop_id']);
            $view = $forum->getViews();
            $view += 1; 
            $forum ->setViews($view);
            $forum->save(); 
        }
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($data);
    }
}
