<?php

namespace Careshop\CommunityIdea\Controller\Idea;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Careshop\CommunityIdea\Model\IdeaFactory;
use Careshop\CommunityIdea\Model\AuthorFactory;
use Careshop\CommunityIdea\Model\TopicFactory;
use Careshop\CommunityIdea\Helper\Data as HelperData;
class AddIdea extends Action
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
	/**
     * @var HelperData
     */
    public $helperData;


    public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        IdeaFactory $ideaFactory,
		AuthorFactory $authorFactory,
        TopicFactory $topicFactory,
		HelperData $helperData
        )
	{
		$this->_pageFactory = $pageFactory;
        $this->ideaFactory = $ideaFactory;
        $this->storeManager = $storeManager;
		$this->authorFactory = $authorFactory;
		$this->helperData    = $helperData;
        $this->topicFactory = $topicFactory;
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
		$customer_id = $this->helperData->getCurrentUser();
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$customer_id) {
            $this->messageManager->addErrorMessage(__('Please login to continue.'));
            $resultRedirect->setPath('customer/account/login');
            return $resultRedirect;
        }
        $data = $this->getRequest()->getPost();
        if(isset($data['post_content']) && isset($data['name'])) { 
            if(trim($data['post_content']) == "") { 
                $this->messageManager->addErrorMessage(__('You need enter the content'));
                $resultRedirect->setPath('community/idea/index');
                return $resultRedirect;
            }
            $content = $data['post_content'];
            if (preg_match_all('/<img([^>]*?)src=(\"|\'|)(.*?)(\"|\'| )(.*?)>/is', $content, $images)) {
                foreach ($images[0] as $key => $image) {
                    $src = $images[3][$key];
                    $replace = 'src=' . $images[2][$key] . $images[3][$key] . $images[4][$key];
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $fileSystem = $objectManager->create('\Magento\Framework\Filesystem');
                    $mediaPath = $fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath();
                    $media = $mediaPath . 'community/forum/';
                    $data_image = $src;
                    list($type, $data_image) = explode(';', $data_image);
                    list(, $data_image)      = explode(',', $data_image);
                    $data_image = base64_decode($data_image);
                    $name = str_replace(" ","_",strtolower($data['name'])). uniqid() . '.png';
                    $url = $media . $name;
                    if (file_put_contents($url, $data_image)) {
                        $url_file = $mediaUrl.'community/forum/'.$name;
                    } else {
                        $url_file = '#';
                    }
                    $newImg = str_replace($replace, 'src="'.$url_file.'"', $image);
                    $content = str_replace($image, $newImg, $content);
                } 
            }
            $ideaData = array(
                'identifier' => time().rand(),
                'name' => ($data['name']) ? $data['name'] : '',
                'customer_id' => $customer_id,
                'post_content' => ($data['post_content']) ? $content : '',
                'categories_ids' => ($data['categories_ids'] ? explode(',', $data['categories_ids']) : array())
            ); 
            $idea = $this->ideaFactory->create();
            $idea->setData($ideaData);
            $idea->save();
        }
        
        $this->messageManager->addSuccessMessage(__('The idea has been added.'));
        $resultRedirect->setPath('community/*/index');
        return $resultRedirect;
    }
}
