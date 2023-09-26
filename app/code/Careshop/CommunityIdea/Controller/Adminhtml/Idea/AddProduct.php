<?php

namespace Careshop\CommunityIdea\Controller\Adminhtml\Idea;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Careshop\CommunityIdea\Controller\Adminhtml\Idea;
use Careshop\CommunityIdea\Helper\Data;
use Careshop\CommunityIdea\Helper\Image;
use Careshop\CommunityIdea\Model\Idea as IdeaModel;
use Careshop\CommunityIdea\Model\IdeaFactory;
use RuntimeException;

class AddProduct extends Idea
{
    /**
     * JS helper
     *
     * @var Js
     */
    public $jsHelper;

    /**
     * @var DateTime
     */
    public $date;

    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    const CHARS_DIGITS = '0123456789';

    protected $resultPageFactory;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param IdeaFactory $ideaFactory
     * @param Js $jsHelper
     * @param Image $imageHelper
     * @param Data $helperData
     * @param DateTime $date
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        Context $context,
        Registry $registry,
        IdeaFactory $ideaFactory,
        Js $jsHelper,
        Image $imageHelper,
        Data $helperData,
        DateTime $date,
        TimezoneInterface $timezone,
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagementInterface,
        PageFactory $resultPageFactory
    ) {
        $this->jsHelper     = $jsHelper;
        $this->_helperData  = $helperData;
        $this->imageHelper  = $imageHelper;
        $this->date         = $date;
        $this->timezone     = $timezone;
        $this->mathRandom   = $mathRandom;
        $this->categoryLinkManagement = $categoryLinkManagementInterface;
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($ideaFactory, $registry, $context);
    }

    public function getRandomNumber($min = 0, $max = null)
    {
        return $this->mathRandom->getRandomNumber($min, $max);
    }

     // Generate Random string
     public function getRandomString($length,  $chars = null)
     {
         return $this->mathRandom->getRandomString($length, $chars);
     }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {  

        $data = $this->getRequest()->getParams() ;
        $post = $this->getRequest()->getPost() ;
		$data1 = $this->getRequest()->getPostValue();
        $random = $this->getRandomString(6,self::CHARS_DIGITS);
        $resultRedirect = $this->resultRedirectFactory->create();
        if(isset($data['id']) && $data['id']) {
            try {
                if(isset($data['image'])) {
                    $imageName = $data['image'][0]['name'];
                    $imagePath = $data['image'][0]['path'].'/'.$imageName;
                    $imageUrl = $data['image'][0]['url'];
                } else {
                    $imagePath = "";
                }
                $idea_id = $data['id'];
                $idea = $this->ideaFactory->create()->load($idea_id);             
                $sku = "CASHOP".$random;
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
                $product = $objectManager->create('\Magento\Catalog\Model\Product');
                $product->setSku($sku); // Set your sku here
                $product->setName($data['name']); // Name of Product
                $product->setAttributeSetId(15); // Attribute set id
                $product->setStatus(1); // Status on product enabled/ disabled 1/0
                $product->setWeight(0); // weight of product
                $product->setVisibility(2); // visibilty of product (catalog / search / catalog, search / Not visible individually)
                $product->setTaxClassId(0); // Tax class id
                $product->setTypeId('configurable'); // type of product (simple/virtual/downloadable/configurable)
                $product->setPrice(isset($data['price']) ? $data['price'] : 0); // price of product
				$product->setDescription($data['description']);
                $product->setShortDescription($data['short_description']);
                $product->setWebsiteIds(array(1));
                if(isset($data['image'])) {
				    $product->addImageToMediaGallery($imagePath, array('image', 'small_image', 'thumbnail'), false, false);
                }
                $product->save();
                $idea->setData('apply_to_product',1);
                $idea->save();

                $categoryIds = [
                    13,
                    14
                ];
                $this->categoryLinkManagement->assignProductToCategories(
                    $product->getSku(),
                    $categoryIds
                );
                
                 
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
                $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
                $connection = $resource->getConnection();
                $tableName = $resource->getTableName('community_idea_product'); //gives table name with prefix
                $product_id =  $product->getId(); 
                $sql = "Insert Into " . $tableName . " (idea_id, entity_id, position) Values ('.$idea_id.','.$product_id.',0)";
                $connection->query($sql);

                $idea_name =  $idea->getName();
                $author_id = $idea->getAuthorId();
                $tableDevelopName = $resource->getTableName('community_develop');
                $sql_community_develop = "Insert Into " . $tableDevelopName . " (name, idea_id, customer_id, product_id) Values ('.$idea_name.','.$idea_id.','.$author_id.','.$product_id.')";
                $connection->query($sql_community_develop);


                $this->messageManager->addSuccessMessage(__('The idea have to apply to product successfull.'));

            } catch (RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('The missing while save product.')
                );
				echo "<pre>"; print_r( $e->getMessage()); echo ($imagePath); die; 
				
            }

        } else {

            $this->messageManager->addErrorMessage(__('Something went wrong while saving the Idea.') );
        
        }


   
        $resultRedirect->setPath('community/*/');

        return $resultRedirect;
    }

    
}
