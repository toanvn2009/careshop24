<?php
 
namespace Rokanthemes\BoughtViewed\Model;
 
class Bought extends \Magento\Framework\Model\AbstractModel
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
	
	public function dataOrderIdByProductID($id_product)
	{ 
		$adapter  = $this->_resource->getConnection(); 
		$sql = "SELECT order_id,product_id,parent_item_id FROM sales_order_item WHERE product_id=".$id_product." AND parent_item_id IS NULL GROUP BY order_id";
		$data_query_order = $adapter->fetchAll($sql);
		return $data_query_order;
	}
	
	public function dataProductIdByOrderID($product_id,$id_order)
	{ 
		$adapter  = $this->_resource->getConnection(); 
		$sql = "SELECT a.order_id,a.product_id,a.parent_item_id,b.sku,COUNT(*) as count FROM sales_order_item a JOIN catalog_product_entity as b ON a.product_id=b.entity_id WHERE a.product_id != ".$product_id." AND a.order_id IN $id_order  AND a.parent_item_id IS NULL GROUP BY a.product_id ORDER BY count DESC LIMIT 8";
		$data_query_product_id = $adapter->fetchAll($sql);
		return $data_query_product_id;
	}
}