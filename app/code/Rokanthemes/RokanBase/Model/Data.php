<?php
 
namespace Rokanthemes\RokanBase\Model;
 
class Data extends \Magento\Framework\Model\AbstractModel
{
	protected $objectManager;  
	protected $_resource;  

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Framework\App\ResourceConnection $resource
    ) {
		$this->objectManager = $objectManager;
        $this->_resource = $resource;
    }
	
	public function getOrders($orders,$email) 
	{ 
		$adapter  = $this->_resource->getConnection(); 
		$sql = "SELECT * FROM sales_order WHERE increment_id='$orders' AND customer_email='$email'"; 
		$data_query_order = $adapter->fetchAll($sql);
		return $data_query_order;
	}
}