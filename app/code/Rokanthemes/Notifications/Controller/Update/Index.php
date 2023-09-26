<?php

namespace Levbon\Affiliate\Controller\Update;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $resultPageFactory;

	public function __construct(
		Context $context,
		PageFactory $resultPageFactory
	)
	{
		$this->resultPageFactory = $resultPageFactory;

		parent::__construct($context);
	}

	public function execute()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$adapter  = $resource->getConnection();
		$sql = 'SELECT entity_id FROM customer_entity';
		$data_query = $adapter->fetchAll($sql);
		foreach ($data_query as $data){
			$sql_cate = 'SELECT account_id FROM affiliate_account WHERE customer_id="'.$data['entity_id'].'"';
			$fetchRow = $adapter->fetchRow($sql_cate);
			if(!$fetchRow){
				$code = $this->generateRandomString(10);
				$tableName1 = $resource->getTableName("affiliate_account");
				$sql1 = "INSERT INTO " . $tableName1 . "(customer_id, code) VALUES (".$data['entity_id'].", '".$code."')";
				$adapter->query($sql1); 
			}
		}
		die;
	}
	public function generateRandomString($length = 12) {
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$adapter  = $resource->getConnection();
		$sql = 'SELECT customer_id FROM affiliate_account WHERE code="'.$randomString.'"';
		$data_query = $adapter->fetchRow($sql);
		if($data_query){
			$this->generateRandomString($length);
		}
		return $randomString;
	}
}
