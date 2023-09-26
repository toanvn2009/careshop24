<?php
namespace Rokanthemes\Notifications\Model;

use Magento\Customer\Model\CustomerFactory;

/**
 * Class Account
 * @package Levbon\Affiliate\Model
 */
class Notifications extends \Magento\Framework\Model\AbstractModel
{
	
	const CACHE_TAG = 'rokanthemes_notifications';

	/**
	 * Event prefix
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'rokanthemes_notifications';

	/**
	 * @type \Mageplaza\Affiliate\Helper\Data
	 */

	/**
	 * @type \Magento\Customer\Model\CustomerFactory
	 */
	protected $_customerFactory;

	/**
	 * Object Manager
	 *
	 * @type
	 */
	protected $objectManager;

	public function __construct(
		\Magento\Framework\Model\Context $context,
		\Magento\Framework\Registry $registry,
		CustomerFactory $customerFactory,
		\Magento\Framework\ObjectManagerInterface $objectmanager,
		\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
		\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
		array $data = []
	)
	{
		$this->_customerFactory = $customerFactory;
		$this->objectManager    = $objectmanager;

		parent::__construct($context, $registry, $resource, $resourceCollection);
	}

	/**
	 * Initialize resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Rokanthemes\Notifications\Model\ResourceModel\Notifications');
	} 
	
	public function loadByCustomerId($customerId){
        $this->load($customerId,'customer_id');
        if(!$this->getId()){
            /*Check if the customer id is exist*/ 
            $customer = $this->_customerFactory->create();
            $customer->load($customerId);
            if(!$customer->getId()) throw new LocalizedException(__("Customer Id is not valid"));
            $this->setData([
               'customer_id' => $customerId 
            ])->setId(null)->save();
        }
        return $this;
    } 
}
