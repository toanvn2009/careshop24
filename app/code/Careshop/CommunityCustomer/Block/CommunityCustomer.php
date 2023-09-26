<?php
namespace Careshop\CommunityCustomer\Block;

use Magento\Framework\View\Element\Template;
use Careshop\CommunityCustomer\Model\ProfileFactory;
use Careshop\CommunityCustomer\Model\SettingsFactory;
use Careshop\CommunityCustomer\Model\TaggingFactory;
use Careshop\CommunityCustomer\Model\AvatarFactory;
use Careshop\CommunityCustomer\Model\SubBookFactory;
use Careshop\CommunityCustomer\Model\MacrosFactory;


class CommunityCustomer extends Template
{


    /**
     * @var ProfileFactory
     */
    protected $profileFactory;

    /**
     * @var SettingFactory
     */
    protected $settingFactory;
    /**
     * @var TaggingFactory
     */
    protected $taggingFactory;

    /**
     * @var AvatarFactory
     */
    protected $avatarFactory;
    /**
     * @var MacrosFactory
     */
    protected $macrosFactory;

    /**
     * @var SubBookFactory
     */
    protected $subBookFactory;
	
    protected $customerSession;

    protected $helperData;

    protected $_storeManager;
	
    /**
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
		Template\Context $context, array $data = [], 
        \Magento\Customer\Model\Session $customerSession,
        \Careshop\CommunityCustomer\Helper\Data $helperData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ProfileFactory $profileFactory,
        SettingsFactory $settingFactory,
        TaggingFactory $taggingFactory,
        AvatarFactory $avatarFactory,
        MacrosFactory $macrosFactory,
        SubBookFactory $subBookFactory
		)
    {
        parent::__construct($context, $data);
        $this->profileFactory = $profileFactory;
        $this->settingFactory = $settingFactory;
        $this->taggingFactory = $taggingFactory;
        $this->avatarFactory = $avatarFactory;
        $this->macrosFactory = $macrosFactory;
        $this->customerSession = $customerSession;
        $this->helperData = $helperData;
        $this->subBookFactory = $subBookFactory;
        $this->_storeManager = $storeManager;
	}
	
	/**
     * Prepare global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
	
        return parent::_prepareLayout();
    }

    /**
     * Prining URLs using StoreManagerInterface
     */
    public function getStoreManagerData()
    {    
        return $this->_storeManager->getStore();
    }

    /**
     * get profile collection 
     *
     * @return $profileCollection
     */
    public function getProfileByCustommerID()
    {
        $profileCollection = null;
        $customer_id = $this->customerSession->getCustomer()->getId(); 
        if($customer_id) {
            $profileCollection = $this->profileFactory->create()
                ->getCollection()
                ->addFieldToFilter('customer_id',$customer_id);
        }

        if($profileCollection){
            return  $profileCollection->getFirstItem();
        } 
        return $profileCollection;

    }

    /**
     * get settings collection 
     *
     * @return $settingCollection
     */
    public function getSettingByCustommerID()
    {
        $settingCollection = null;
        $customer_id = $this->customerSession->getCustomer()->getId(); 
        if($customer_id) {
            $settingCollection = $this->settingFactory->create()
                ->getCollection()
                ->addFieldToFilter('customer_id',$customer_id);
        }

        if($settingCollection){
            return  $settingCollection->getFirstItem();
        } 
        return $settingCollection;

    }

    /**
     * get Taggings collection 
     *
     * @return $taggingCollection
     */
    public function getTaggingByCustomerId()
    {
        $taggingCollection = null;
        $customer_id = $this->customerSession->getCustomer()->getId(); 
        if($customer_id) {
            $taggingCollection = $this->taggingFactory->create()
                ->getCollection()
                ->addFieldToFilter('customer_id',$customer_id);
        }

        if($taggingCollection){
            return  $taggingCollection->getFirstItem();
        } 
        return $taggingCollection;

    }
    /**
     * get Subscription & Bookmarks collection 
     *
     * @return $subbookCollection
     */
    public function getSubscriptionBookmarks()
    {
        $subbookCollection = null;
        $customer_id = $this->customerSession->getCustomer()->getId(); 
        if($customer_id) {
            $subbookCollection = $this->subBookFactory->create()
                ->getCollection()
                ->addFieldToFilter('customer_id',$customer_id);
        }

        if($subbookCollection){
            return  $subbookCollection->getFirstItem();
        } 
        return $subbookCollection;
    }

    /**
     * get Macros collection 
     *
     * @return $macrosCollection
     */
    public function getMacrosByCustomerId() 
    {
        $macrosCollection = null;
        $customer_id = $this->customerSession->getCustomer()->getId(); 
        if($customer_id) {
            $macrosCollection = $this->macrosFactory->create()
                ->getCollection()
                ->addFieldToFilter('customer_id',$customer_id);
        }

        if($macrosCollection){
            return  $macrosCollection->getFirstItem();
        } 
        return $macrosCollection;
    }


    /**
     * get Avatar collection 
     *
     * @return $avatarCollection
     */
    public function getAvatars()
    {
        $avatarCollection = null;
            $avatarCollection = $this->avatarFactory->create()
                ->getCollection();
         return $avatarCollection;

    }

    /**
     * get Email  from customer 
     *
     * @return $email
     */
    public function getEmail() {
        $email = null;
        if( $this->customerSession->getCustomer()->getId() ) {
            $email = $this->customerSession->getCustomer()->getEmail(); 
        }
        return $email;
    }

    public function getHelper()
    {
        return $this->helperData;
    }

	
}

