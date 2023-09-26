<?php
 
namespace Rokanthemes\Repair\Model;
 
class Repair extends \Magento\Framework\Model\AbstractModel
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
	
	public function getDataSearch($id,$lastname,$customer_id) 
	{ 
		$adapter  = $this->_resource->getConnection(); 
		$sql = "SELECT * FROM sales_order WHERE increment_id LIKE '%$id%' AND customer_lastname LIKE '%$lastname%' AND customer_id='$customer_id'"; 
		$data_query = $adapter->fetchAll($sql);
		return $data_query;
	}
}