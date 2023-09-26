<?php

namespace Careshop\CommunityIdea\Controller\Topic;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Careshop\CommunityIdea\Model\IdeaFactory;
use Careshop\CommunityIdea\Model\AuthorFactory;
use Careshop\CommunityIdea\Model\LikeFactory;
use Careshop\CommunityIdea\Helper\Data as HelperData;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;

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

    protected $likeFactory;
	/**
     * @var HelperData
     */
    public $helperData;
    private $json;
    private $resultJsonFactory;

    protected $scopeConfig;
    protected $transportBuilder;
    protected $inlineTranslation;
    public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        IdeaFactory $ideaFactory,
		AuthorFactory $authorFactory,
        LikeFactory $likeFactory,
		HelperData $helperData,
        TransportBuilder $transportBuilder,
        StateInterface $state
        )
	{
		$this->_pageFactory = $pageFactory;
        $this->ideaFactory = $ideaFactory;
        $this->storeManager = $storeManager;
		$this->authorFactory = $authorFactory;
		$this->helperData    = $helperData;
        $this->likeFactory = $likeFactory;
        $this->json = $json;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $state;
        $this->_scopeConfig = $scopeConfig;
		return parent::__construct($context);
	}

    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    public function getStoreEmail()
    {
        return $this->_scopeConfig->getValue(
            'trans_email/ident_sales/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface|Page
     */
    public function execute()
    {   
        $reportData = $this->getRequest()->getPost();
        $toEmail =  $this->getStoreEmail();
        // this is an example and you can change template id,fromEmail,toEmail,etc as per your need.
        $templateId = 9; // template id
        $fromName = 'Admin';             
        $userData = $this->helperData->getUserData();
        $resultJson = $this->resultJsonFactory->create();
        $fromEmail="";
        if($userData) {
            $fromEmail = $userData->getEmail(); 
        }
        if(!$fromEmail){
            $this->messageManager->addErrorMessage(__('You need login to report'));
            return $resultJson->setData($reportData);
        }
 
         try {
             // template variables pass here
             $templateVars = [
                 'topic_id' => $reportData['topic_id'],
                 'report_content' => $reportData['report_content']
             ];
 
             $storeId = $this->storeManager->getStore()->getId();
 
             $from = ['email' => $fromEmail, 'name' => $fromName];
             $this->inlineTranslation->suspend();
 
             $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
             $templateOptions = [
                 'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                 'store' => $storeId
             ];
             $transport = $this->transportBuilder->setTemplateIdentifier($templateId, $storeScope)
                 ->setTemplateOptions($templateOptions)
                 ->setTemplateVars($templateVars)
                 ->setFrom($from)
                 ->addTo($toEmail)
                 ->getTransport();
             $transport->sendMessage();
             $this->inlineTranslation->resume();
             $this->messageManager->addSuccessMessage(__('Report Success. An email sent to Admin'));
         } catch (\Exception $e) {
            //echo "<pre>";print_r($e->getMessage()); die('ss');
         }

        return $resultJson->setData($reportData);
    }
}
