<?php
namespace Rokanthemes\ReturnsWarranty\Model;

use Magento\Customer\Model\CustomerFactory;

/**
 * Class Account
 * @package Levbon\Affiliate\Model
 */
class Warranty extends \Magento\Framework\Model\AbstractModel
{
	
	const CACHE_TAG = 'rokanthemes_returnswarranty';

	/**
	 * Event prefix
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'rokanthemes_returnswarranty';

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
	protected $_resourceConnection;
	protected $customerSession;

	public function __construct(
		\Magento\Framework\Model\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Customer\Model\Session $customerSession,
		CustomerFactory $customerFactory,
		\Magento\Framework\ObjectManagerInterface $objectmanager,
		\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
		\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
		\Magento\Framework\App\ResourceConnection $_resourceConnection,
		array $data = []
	)
	{
		$this->_customerFactory = $customerFactory;
		$this->objectManager    = $objectmanager;
		$this->customerSession = $customerSession;
		$this->_resourceConnection = $_resourceConnection;
		parent::__construct($context, $registry, $resource, $resourceCollection);
	}

	/**
	 * Initialize resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Rokanthemes\ReturnsWarranty\Model\ResourceModel\Warranty');
	} 
	
	public function getDataWarrantyByOrderIdItem($id) 
	{  
		$adapter  = $this->_resourceConnection->getConnection(); 
		$sql = 'SELECT entity_id FROM warranty WHERE order_item_id="'.$id.'"';
		$data_query = $adapter->fetchRow($sql);
		return $data_query;
	}
	public function getDataWarrantys() 
	{  
		$adapter  = $this->_resourceConnection->getConnection(); 
		$customer_id = $this->customerSession->getId();
		$sql = 'SELECT * FROM warranty WHERE customer_id="'.$customer_id.'"';
		$data_query = $adapter->fetchAll($sql);
		return $data_query;
	}
	
	public function getDataOrder($id) 
	{  
		$adapter  = $this->_resourceConnection->getConnection(); 
		$customer_id = $this->customerSession->getId();
		$sql = "SELECT sales_order_item.*,sales_order.customer_id,sales_order.entity_id FROM sales_order_item INNER JOIN sales_order ON sales_order_item.order_id = sales_order.entity_id WHERE sales_order_item.item_id='$id' AND sales_order.customer_id='$customer_id'"; 
		$data_query = $adapter->fetchRow($sql);
		return $data_query;
	}
	
	public function getDataSearchOrder($id) 
	{  
		$adapter  = $this->_resourceConnection->getConnection(); 
		$customer_id = $this->customerSession->getId();
		$sql = "SELECT * FROM sales_order WHERE increment_id LIKE '%$id%' AND customer_id='$customer_id'"; 
		$data_query = $adapter->fetchAll($sql);
		return $data_query;
	}
	
	public function getDataSearchOrderByDate($orders,$startdate,$endorders) 
	{  
		$adapter  = $this->_resourceConnection->getConnection(); 
		$customer_id = $this->customerSession->getId();
		$sql = "SELECT * FROM sales_order WHERE increment_id LIKE '%$orders%' AND customer_id='$customer_id' AND created_at BETWEEN CAST('$startdate' AS DATE) AND CAST('$endorders' AS DATE)"; 
		$data_query = $adapter->fetchAll($sql);
		return $data_query;
	}
	
	public function getDataOrderByDataQuery($data_query) 
	{  
		$adapter  = $this->_resourceConnection->getConnection(); 
		$sql_data = "SELECT entity_id FROM warranty WHERE order_id='".$data_query['order_id']."' AND order_item_id='".$data_query['item_id']."' AND product_id='".$data_query['product_id']."'"; 
		$data_query_data = $adapter->fetchRow($sql_data);
		return $data_query_data;
	}
}
