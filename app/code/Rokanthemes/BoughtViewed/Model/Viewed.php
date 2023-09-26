<?php
 
namespace Rokanthemes\BoughtViewed\Model;
 
class Viewed extends \Magento\Framework\Model\AbstractModel
{
	
	protected $objectManager;  
	protected $remoteAddress;  
	protected $_resourceConnection;  
	
	public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
		\Magento\Framework\App\ResourceConnection $_resourceConnection,
        array $data = []
    ) {
		$this->objectManager = $objectManager;
		$this->remoteAddress = $remoteAddress;
		$this->_resourceConnection = $_resourceConnection;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
   
    protected function _construct()
    {
        $this->_init('Rokanthemes\BoughtViewed\Model\ResourceModel\Viewed');
    }
	
	public function loadByIpAddress()
	{ 
		$ip_address = $this->getIpAddress();
		return $this->load($ip_address, 'ip_address');
	}
	
	public function getIpAddress()
	{ 
		$ip = $this->remoteAddress->getRemoteAddress();
		return $ip;
	}
	
	public function dataQueryProductId($data_time,$ip,$product_id)
	{ 
		$adapter  = $this->_resourceConnection->getConnection(); 
		$time = date('Y-m-d H:i:s',strtotime('-360000 minutes',strtotime($data_time)));
		$sql = "SELECT product_id,created_at,ip_address, COUNT(*) as count FROM product_bought_viewed WHERE (created_at BETWEEN '".$time."' AND '".$data_time."') AND ip_address != '".$ip."' AND product_id !=".$product_id." GROUP BY product_id ORDER BY count DESC LIMIT 8";
		$data_query_product_id = $adapter->fetchAll($sql);
		return $data_query_product_id;
	}
}