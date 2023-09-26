<?php
namespace Rokanthemes\Reviews\Model;

use Magento\Customer\Model\CustomerFactory;

/**
 * Class Account
 * @package Levbon\Affiliate\Model
 */
class Improve extends \Magento\Framework\Model\AbstractModel
{
	
	const CACHE_TAG = 'rokanthemes_reviews';

	/**
	 * Event prefix
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'rokanthemes_reviews';

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
		$this->_init('Rokanthemes\Reviews\Model\ResourceModel\Improve');
	} 
}
