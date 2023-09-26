<?php
 
namespace Careshop\CommunityCustomer\Controller\Customer;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Filesystem;
use Careshop\CommunityCustomer\Model\ProfileFactory;
use Careshop\CommunityCustomer\Model\SettingsFactory;
use Careshop\CommunityCustomer\Model\TaggingFactory;
use Careshop\CommunityCustomer\Model\AvatarFactory;
use Careshop\CommunityCustomer\Model\SubBookFactory;
use Careshop\CommunityCustomer\Model\MacrosFactory;
use Careshop\CommunityCustomer\Model\ResourceModel\Profile\CollectionFactory;

class Save extends \Magento\Framework\App\Action\Action
{
    private $json;
    private $resultJsonFactory;
    
    /**
     * @var ProfileFactory
     */
    private $profileFactory;
	 /**
     * @var SettingsFactory
     */
    private $settingsFactory;
    /**
     * @var TaggingFactory
     */
    private $taggingFactory;
    /**
     * @var AvatarFactory
     */
    private $avatarFactory;
    /**
     * @var SubBookFactory
     */
    private $subBookFactory;
    /**
     * @var MacrosFactory
     */
    private $macrosFactory;
    /**
     * @var CollectionFactory
     */
    private $collectionProfileFactory;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var UploaderFactory
     */
	private $uploaderFactory;
	
	/**
     * @var AdapterFactory
     */
    private $adapterFactory;
	
	/**
     * @var Filesystem
     */
    private $filesystem;

    private $_storeManager;
 
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ProfileFactory $profileFactory,
		SettingsFactory $settingsFactory,
        TaggingFactory $taggingFactory,
        AvatarFactory $avatarFactory,
        SubBookFactory $subBookFactory,
        MacrosFactory $macrosFactory,
        CollectionFactory $collectionProfileFactory,
        UploaderFactory $uploaderFactory,
        AdapterFactory $adapterFactory,
        Filesystem $filesystem
    )
    {
        $this->json = $json;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->profileFactory = $profileFactory;
        $this->customerSession = $customerSession;
        $this->collectionProfileFactory = $collectionProfileFactory;
		$this->settingsFactory = $settingsFactory;
        $this->taggingFactory = $taggingFactory;
        $this->avatarFactory = $avatarFactory;
        $this->subBookFactory = $subBookFactory;
        $this->macrosFactory = $macrosFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem; 
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }
    
    public function execute()
    {         
        $profile_data = array();
        $response = $this->getRequest()->getParams();
        $profile = $this->profileFactory->create();
        $setting = $this->settingsFactory->create();
        $tagging = $this->taggingFactory->create();
        $avatar =  $this->avatarFactory->create();
        $subBook =  $this->subBookFactory->create();
        $macros =  $this->macrosFactory->create();
        $media_url = $this->_storeManager-> getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
		$customer_id = $this->customerSession->getCustomer()->getId(); 
        $profileCollection = null; 
		$settingCollection = null;
        $taggingCollection = null;
        $subbookCollection = null;
        $profile_id = "";
		$setting_id = "";
        $tagging_id = "";
        $subbook_id = "";		
    	$macros_id = "";
        if($customer_id) {
            $profileCollection = $profile
                ->getCollection()
                ->addFieldToFilter('customer_id',$customer_id);
				
			$settingCollection = $setting
                ->getCollection()
                ->addFieldToFilter('customer_id',$customer_id);	

            $taggingCollection = $tagging
                ->getCollection()
                ->addFieldToFilter('customer_id',$customer_id);

            $subbookCollection = $subBook
                ->getCollection()
                ->addFieldToFilter('customer_id',$customer_id);
            $macrosCollection = $macros
                ->getCollection()
                ->addFieldToFilter('customer_id',$customer_id);	     	       	    
        }   
        
	    if($profileCollection->count()){
			 $profile_id = $profileCollection->getFirstItem()['profile_id'];
		}
		 
		if($settingCollection->count()){
			 $setting_id = $settingCollection->getFirstItem()['setting_id'];
		}

        if($taggingCollection->count()){
            $tagging_id = $taggingCollection->getFirstItem()['tagging_id'];
        }

        if($subbookCollection->count()){
            $subbook_id = $subbookCollection->getFirstItem()['subbook_id'];
        }

        if($macrosCollection->count()){
            $macros_id = $macrosCollection->getFirstItem()['macros_id'];
        }
  
        //upload avatar
        if(isset( $_FILES['file'] )){
            $files = $_FILES['file'];
            if (!empty($files["name"])){
                try{
                   
                    $data= null;
                    $imagePath = "";
                    $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'file']);
                    //check upload file type or extension
                    $uploaderFactory->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                    $imageAdapter = $this->adapterFactory->create();
                    $uploaderFactory->addValidateCallback('custom_image_upload',$imageAdapter,'validateUploadFile');
                    $uploaderFactory->setAllowRenameFiles(true);
                    $uploaderFactory->setFilesDispersion(true);
                    $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                    $destinationPath = $mediaDirectory->getAbsolutePath('avatars');
                    $result = $uploaderFactory->save($destinationPath);
                   
                    if (!$result) {
                        throw new LocalizedException(
                            __('File cannot be saved to path: $1', $destinationPath)
                        );
                    }
                    $imagePath = 'avatars'.$result['file'];                    
                    //Set file path with name for save into database
                    $data['file'] = $imagePath;
                    if($profile_id) {
                        $profile = $profile->load($profile_id); 
                        $profile->setAvatar($imagePath);
                        $profile->save();
                     
                    } else {
                        $profile_data = array('avatar' =>$imagePath );
                        $profile->addData($profile_data );
                        $profile->save();
                    }
                    //save image to gallery avatar
                    if($imagePath) {
                        $avatar_data = array('file' =>$imagePath );
                        $avatar->addData($avatar_data);
                        $avatar->save();
                    }
                    $data['media_url'] = $media_url;
                    $data['avatar_url'] = $media_url.$imagePath;
                    $this->messageManager->addSuccessMessage(__('Upload success').$imagePath);
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
                $resultJson = $this->resultJsonFactory->create();
                return $resultJson->setData($data);
            }
        }
        //update avatar default
        if(isset( $response['type'] ) && $response['type'] == 'update_avatar'){
            if($profile_id) {
                $profile = $profile->load($profile_id); 
                $profile->setAvatar($response['avatar'] );
                $profile->save();
                $data['avatar_url'] = $media_url.$response['avatar'];
                $this->messageManager->addSuccessMessage(__('Upload Avatar success'));
                $resultJson = $this->resultJsonFactory->create();
                return $resultJson->setData($data);
            } 
        }
        //update information for form data cummunity settings
        if(isset( $response['formdata'] )){
            $formdata = $response['formdata'];
            $action = $response['action'];

            if ($action == 'update-profile') {
                $profile_data = array(
                    'email' =>  $formdata['new_email'],
                    'customer_id' => $this->customerSession->getCustomer()->getId(),
                    'signature' =>  $formdata['signiture'],
                    'title' =>  $formdata['title'],
                    'place' =>  $formdata['place'],
                    'website' =>  $formdata['website'],
                    'biography' =>  $formdata['biography'],
                    'private' =>  $formdata['private'],
                    'icq_number' =>  $formdata['tcq_number'],
                    'acs_name' =>  $formdata['acs_screen_name'],
                    'msn_name' =>  $formdata['messenger_screen_name'],
                    'yahoo_id' =>  $formdata['yahoo_id'],
                    'sky_name' =>  $formdata['skype_name']
                );
            }

            if ($action == 'update-setting') {
                $setting_data = array(
                    'customer_id' => $this->customerSession->getCustomer()->getId(),
                    'option_time_zone' =>  $formdata['option_time_zone'],
                    'option_ignore_html' => (isset( $formdata['option_ignore_html'] )) ? 1 : 0,
                    'option_show_signature' => (isset( $formdata['option_show_signature']) ) ? 1 : 0,
                    'option_editor_post' => (isset($formdata['option_editor_post'])) ? $formdata['option_editor_post'] : 0,
                    'option_auto_subscribe' => (isset( $formdata['option_auto_subscribe'] )) ? 1 : 0,
                    'option_confirm_submit' => (isset( $formdata['option_confirm_submit'] )) ? 1 : 0,
                    'display_time_zone' => (isset( $formdata['display_time_zone'] )) ? $formdata['display_time_zone'] : '',
                    'display_board_layout' => (isset( $formdata['display_board_layout'] )) ? $formdata['display_board_layout'] : '',
                    'display_emoticon' => (isset( $formdata['display_emoticon'] )) ? $formdata['display_emoticon'] : '',
                    'display_menu' => (isset( $formdata['display_menu'] )) ? 1 : 0,
                    'display_show_folder' => (isset( $formdata['display_show_folder'] )) ? 1 : 0,
                    'display_date_format' => (isset( $formdata['display_date_format'] )) ? $formdata['display_date_format'] : '',
                    'display_date_type' => (isset( $formdata['display_date_type'] )) ? $formdata['display_date_type'] : '',
                    'display_quick_response' => (isset( $formdata['display_quick_response'] )) ? 1 : 0,
                    'display_topic_style' => (isset( $formdata['display_topic_style'] )) ? $formdata['display_topic_style'] : '',
                    'display_liststyle_opt' => (isset( $formdata['display_liststyle_opt'] )) ? $formdata['display_liststyle_opt'] : '',
                    'default_flag_topic' => (isset( $formdata['default_flag_topic'] )) ? $formdata['default_flag_topic'] : '',
                    'default_auto_tagging' => (isset( $formdata['default_auto_tagging'] )) ? $formdata['default_auto_tagging'] : '',
                    'default_sort_topics' => (isset( $formdata['default_sort_topics'] )) ? $formdata['default_sort_topics'] : '',
                    'lenear_sort_topic' => (isset( $formdata['lenear_sort_topic'] )) ? $formdata['lenear_sort_topic'] : '',
                    'lenear_topics_per' => (isset( $formdata['lenear_topics_per'] )) ? $formdata['lenear_topics_per'] : '',
                    'lenear_post_per' => (isset( $formdata['lenear_post_per'] )) ? $formdata['lenear_post_per'] : '',
                    'lenear_jumb_first' => (isset( $formdata['lenear_jumb_first'] )) ? 1 : 0,
                    'lenear_filter_qa' => (isset( $formdata['lenear_filter_qa'] )) ? $formdata['lenear_filter_qa'] : '',
                    'lenear_sort_qa' => (isset( $formdata['lenear_sort_qa'] )) ? $formdata['lenear_sort_qa'] : '',
                    'theme_sort_topic' => (isset( $formdata['theme_sort_topic'] )) ? $formdata['theme_sort_topic'] : '',
                    'theme_contributions_per' => (isset( $formdata['theme_contributions_per'] )) ? $formdata['theme_contributions_per'] : '',
                    'theme_posts_per' => (isset( $formdata['theme_posts_per'] )) ? $formdata['theme_posts_per'] : '',
                    'home_number_bookmarks' => (isset( $formdata['home_number_bookmarks'] )) ? $formdata['home_number_bookmarks'] : 10,
                    'home_number_friends' => (isset( $formdata['home_number_friends'] )) ? $formdata['home_number_friends'] : 10,
                    'home_number_pmessages' => (isset( $formdata['home_number_pmessages'] )) ? $formdata['home_number_pmessages'] : 10,
                    'home_number_subscr' => (isset( $formdata['home_number_subscr'] )) ? $formdata['home_number_subscr'] : 10,
                    'home_location_sb' => (isset( $formdata['home_location_sb'] )) ? 1: 0,
                    'home_post_sb' => (isset( $formdata['home_post_sb'] )) ? 1: 0,
                    'home_component_ltopics' => (isset( $formdata['home_component_ltopics'] )) ? 1: 0,
                    'home_component_lposts' => (isset( $formdata['home_component_lposts'] )) ? 1: 0,
                    'home_private_community' => (isset( $formdata['home_private_community'] )) ? $formdata['home_private_community'] : '',
                    'home_my_friends' => (isset( $formdata['home_my_friends'] )) ? $formdata['home_my_friends'] : '',
                    'privacy_person_data' => (isset( $formdata['privacy_person_data'] )) ? $formdata['privacy_person_data'] : '',
                    'privacy_show_email' => (isset( $formdata['privacy_show_email'] )) ? $formdata['privacy_show_email'] : '',
                    'privacy_online_status' => (isset( $formdata['privacy_online_status'] )) ? $formdata['privacy_online_status'] : '',
                    'pm_status' => (isset( $formdata['pm_status'] )) ? 1: 0,
                    'pm_numbers_per' => (isset( $formdata['pm_numbers_per'] )) ? $formdata['pm_numbers_per']: 10,
                    'pm_email_notify' => (isset( $formdata['pm_email_notify'] )) ? 1: 0,
                    'likes_buttons_sums' => (isset( $formdata['likes_buttons_sums'] )) ? 1 : 0,
                    'likes_period_rec' => (isset( $formdata['likes_period_rec'] )) ? $formdata['likes_period_rec'] : '',
                    'likes_period_aut' => (isset( $formdata['likes_period_aut'] )) ? $formdata['likes_period_aut'] : '',
                    'mobile_homepage' => (isset( $formdata['mobile_homepage'] )) ? $formdata['mobile_homepage'] : '',
                    'mobile_pictures' => (isset( $formdata['mobile_pictures'] )) ? $formdata['mobile_pictures'] : 0, 
                );
            }

            if ($action == 'update-tagging') {
                $tagging_data = array(
                    'customer_id' => $this->customerSession->getCustomer()->getId(),
                    'tagging_active' => (isset( $formdata['tagging_active'] )) ? 1 : 0,
                    'tagging_seperator' => (isset( $formdata['tagging_seperator'] )) ? 1 : 0,
                    'tagging_show_num' => (isset( $formdata['tagging_show_num'] )) ? 1 : 0,
                    'tagging_number' => (isset( $formdata['tagging_number'] )) ? $formdata['tagging_number'] : 10,
                    'tagging_number_top' => (isset( $formdata['tagging_number_top'] )) ? $formdata['tagging_number_top'] : 10,
                    'tagging_page' => (isset( $formdata['tagging_page'] )) ? $formdata['tagging_page'] : 10,
                    'tagging_size_cloud' => (isset( $formdata['tagging_size_cloud'] )) ? $formdata['tagging_size_cloud'] : 10,
                    'tagging_size_tagger' => (isset( $formdata['tagging_size_tagger'] )) ? $formdata['tagging_size_tagger'] : 10,
                    'tagging_cloud_posts' => (isset( $formdata['tagging_cloud_posts'] )) ? $formdata['tagging_cloud_posts'] : 10,
                    'tagging_cloud_filter' => (isset( $formdata['tagging_cloud_filter'] )) ? $formdata['tagging_cloud_filter'] : '',
                    'tagging_cloud_age' => (isset( $formdata['tagging_cloud_age'] )) ? $formdata['tagging_cloud_age'] : '',
                );
            }

            if ($action == 'update-subbook') {
                $subscription_bookmarks = array(
                    'customer_id' => $this->customerSession->getCustomer()->getId(),
                    'email_html_posts' => (isset( $formdata['email_html_posts'] )) ? 1 : 0,
                    'email_user_posts' => (isset( $formdata['email_user_posts'] )) ? 1 : 0,
                    'email_content' => (isset( $formdata['email_content'] )) ? $formdata['email_content'] : '',
                    'email_format' => (isset( $formdata['email_format'] )) ? $formdata['email_format'] : '',
                    'notify_post_rating' => (isset( $formdata['notify_post_rating'] )) ? 1 : 0,
                    'notify_me_submit' => (isset( $formdata['notify_me_submit'] )) ? 1 : 0,
                    'notify_auto_sub' => (isset( $formdata['notify_auto_sub'] )) ? 1 : 0,
                    'notify_regard_forum' => (isset( $formdata['notify_regard_forum'] )) ? $formdata['notify_regard_forum'] : '',
                    'notify_topic_article' => (isset( $formdata['notify_topic_article'] )) ? $formdata['notify_topic_article'] : '',
                    'notify_new_answer' => (isset( $formdata['notify_new_answer'] )) ? $formdata['notify_new_answer'] : '',
                    'notify_replied_answer' => (isset( $formdata['notify_replied_answer'] )) ? $formdata['notify_replied_answer'] : '',
                    'notify_contribution' => (isset( $formdata['notify_contribution'] )) ? $formdata['notify_contribution'] : '',
                    'notify_accept_solution' => (isset( $formdata['notify_accept_solution'] )) ? 1 : 0,
                    'notify_sign_deserve' => (isset( $formdata['notify_sign_deserve'] )) ? $formdata['notify_sign_deserve'] : '',
                    'notify_all_edits' => (isset( $formdata['notify_all_edits'] )) ? $formdata['notify_all_edits'] : '',
                    'notify_edit_publish' => (isset( $formdata['notify_edit_publish'] )) ? $formdata['notify_edit_publish'] : '',
                    'notify_edit_subscribe' => (isset( $formdata['notify_edit_subscribe'] )) ? $formdata['notify_edit_subscribe'] : '',
                    'notify_rating' => (isset( $formdata['notify_rating'] )) ? 1 : 0,
                    'notify_public_article' => (isset( $formdata['notify_public_article'] )) ? 1 : 0,
                    'notify_like_posts' => (isset( $formdata['notify_like_posts'] )) ? $formdata['notify_like_posts'] : '',
                );
            }

            if ($action == 'update-macros') {
                $macros_data = array(
                    'customer_id' => $this->customerSession->getCustomer()->getId(),
                    'title_1' => (isset( $formdata['title_1'] )) ? $formdata['title_1'] : '',
                    'title_2' => (isset( $formdata['title_2'] )) ? $formdata['title_2'] : '',
                    'title_3' => (isset( $formdata['title_3'] )) ? $formdata['title_3'] : '',
                    'title_4' => (isset( $formdata['title_4'] )) ? $formdata['title_4'] : '',
                    'title_5' => (isset( $formdata['title_5'] )) ? $formdata['title_5'] : '',
                    'title_6' => (isset( $formdata['title_6'] )) ? $formdata['title_6'] : '',
                    'title_7' => (isset( $formdata['title_7'] )) ? $formdata['title_7'] : '',
                    'title_8' => (isset( $formdata['title_8'] )) ? $formdata['title_8'] : '',
                    'title_9' => (isset( $formdata['title_9'] )) ? $formdata['title_9'] : '',
                );
            }
        }

        $status_update = "";

        try {
            if ($action == 'update-profile') {
                if($profile_id) {
                    $profile = $profile->load($profile_id); 
                    $profile->addData($profile_data);
                    $profile->save();
                } else {
                    $profile->addData($profile_data);
                    $profile->save();
                }
            }
            
            if ($action == 'update-setting') {
                if($setting_id) {
                    $setting = $setting->load($setting_id); 
                    $setting->addData($setting_data);
                    $setting->save();
                } else {
                    $setting->addData($setting_data);
                    $setting->save();
                }
            }
       
            if ($action == 'update-tagging') {
                if($tagging_id) {
                    $tagging = $tagging->load($tagging_id); 
                    $tagging->addData($tagging_data);
                    $tagging->save();

                } else {
                    $tagging->addData($tagging_data);
                    $tagging->save();
                }
            }

            if ($action == 'update-subbook') {
                if($subbook_id) {
                    $subBook = $subBook->load($subbook_id); 
                    $subBook->addData($subscription_bookmarks);
                    $subBook->save();
                
                } else {
                    $subBook->addData($subscription_bookmarks);
                    $subBook->save();
                }
            }

            if ($action == 'update-macros') {
                if($macros_id) {
                    $macros = $macros->load($macros_id); 
                    $macros->addData($macros_data);
                    $macros->save();
                
                } else {
                    $macros->addData($macros_data);
                    $macros->save();
                }
            }

            $this->messageManager->addSuccessMessage(__('Update Success.'));

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $message = __('Something went wrong while saving.');
            $this->messageManager->addExceptionMessage($e, $message);
        }
		
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
}