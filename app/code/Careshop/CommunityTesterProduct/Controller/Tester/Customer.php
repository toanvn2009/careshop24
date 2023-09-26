<?php

namespace Careshop\CommunityTesterProduct\Controller\Tester;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Careshop\CommunityTesterProduct\Model\TesterFactory;
use Careshop\CommunityTesterProduct\Model\TesterProductFactory;

class Customer extends Action
{
    /**
     * Tester Factory
     *
     * @var TesterFactory
     */
    protected $testerFactory;

    /**
     * Tester Product Factory
     *
     * @var TesterProductFactory
     */
    protected $testerProductFactory;

    protected $_pageFactory;

    protected $storeManager;
	
	protected $authorFactory;

    protected $commentFactory;
	/**
     * @var HelperData
     */
    public $helperData;
    protected $_customerSession; 
    protected $_resource; 
    protected $resultJsonFactory;


    public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Customer\Model\Session $session,
        TesterFactory $testerFactory,
        TesterProductFactory $testerProductFactory
        )
	{
		$this->_pageFactory = $pageFactory;
        $this->testerFactory = $testerFactory;
        $this->testerProductFactory = $testerProductFactory;
        $this->_customerSession = $session;
        $this->_resource = $resource;
        $this->storeManager = $storeManager;
        $this->resultJsonFactory = $resultJsonFactory;
		return parent::__construct($context);
	}

    /**
     * @return ResponseInterface|Redirect|ResultInterface|Page
     */
    public function execute()
    {   
        $connection = $this->_resource->getConnection();
        $tableName = $this->_resource->getTableName('community_tester');
        $data = $this->getRequest()->getPost();
        $tester_product = $this->testerProductFactory->create();
        try {
            if ($this->_customerSession->isLoggedIn()) {
                $community_tester = "Select * FROM " . $tableName . " WHERE product_id=".$data['product_id']."";
                $result = $connection->fetchRow($community_tester);
                if (isset($result['tester_id']) && $result['tester_id']) {
                    $data_add = array(
                        'tester_id' => ($result['tester_id']) ? $result['tester_id'] : '',
                        'customer_id' => $this->_customerSession->getCustomer()->getId(),
                        'product_id' => ($data['product_id']) ? $data['product_id'] : '',
                    );
                    $tester_product->setData($data_add);
                    $tester_product->save();
                    $data = array(
                        'error' => false
                    );
                } else {
                    $data = array(
                        'error' => true
                    );
                }
            } else {
                $data = array(
                    'error' => true,
                    'login' => true,
                );
            }
        } catch (\Exception $e) {
            $data = array(
                'error' => true
            );
        }
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($data);
    }

}
