<?php

namespace Careshop\CommunityDevelopProduct\Controller\Forum;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Careshop\CommunityDevelopProduct\Model\DevelopFactory;
use Careshop\CommunityDevelopProduct\Model\ForumFactory;
use Careshop\CommunityDevelopProduct\Model\ReportForumFactory;

class Report extends Action
{
    /**
     * Idea Factory
     *
     * @var IdeaFactory
     */
    protected $ideaFactory;

    protected $_pageFactory;

    protected $storeManager;
	
	protected $authorFactory;

    protected $commentFactory;
	/**
     * @var HelperData
     */
    public $helperData;


    public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        ForumFactory $forumFactory,
        DevelopFactory $developFactory,
        ReportForumFactory $reportForumFactory
        )
	{
		$this->_pageFactory = $pageFactory;
        $this->forumFactory = $forumFactory;
        $this->developFactory = $developFactory;
        $this->reportForumFactory = $reportForumFactory;
        $this->_productloader = $_productloader;
        $this->storeManager = $storeManager;
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
        $report = $this->reportForumFactory->create();
        $reportData = array(
            'forum_id' => ($data['forum_id']) ? $data['forum_id'] : '',
            'customer_id' => ($data['customer_id']) ? $data['customer_id'] : '',
            'description' => ($data['report_content']) ? $data['report_content'] : ''
        );
        $report->setData($reportData);
        $report->save();
        $resultRedirect = $this->resultRedirectFactory->create();
        $develop = $this->developFactory->create()->load($data['develop_id']);
        $_product = $this->getProduct($develop->getProductId());
        $resultRedirect->setPath($_product->getProductUrl());
		$this->messageManager->addSuccessMessage(__('You report Succesfully!'));
        return $this->getResponse();
    }

    public function getProduct($product_id)
    {
        $_product = $this->_productloader->create()->load($product_id);
        return $_product;
    }

}
