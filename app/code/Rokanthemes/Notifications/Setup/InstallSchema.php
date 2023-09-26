<?php

namespace Rokanthemes\Notifications\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
	/**
	 * install tables
	 *
	 * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
	 * @param \Magento\Framework\Setup\ModuleContextInterface $context
	 * @return void
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();
		$connection = $installer->getConnection();

		if ($installer->tableExists('rokanthemes_notifications')) {
			$connection->dropTable($installer->getTable('rokanthemes_notifications'));
		}
		$table = $connection->newTable($installer->getTable('rokanthemes_notifications'))
			->addColumn(
				'notifications_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null,
				['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
				'Account ID'
			)->addColumn(
				'customer_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable => false'], 'Account Customer ID'
			)->addColumn(
				'package_shipment', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['nullable'=>false,'default'=>1], 'Package Shipment'
			)->addColumn(
				'order_confirmation', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['nullable'=>false,'default'=>1], 'Order Confirmation'
			)->addColumn(
				'status_update', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['nullable'=>false,'default'=>1], 'Status Update'
			)->addColumn(
				'promotions', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['nullable'=>false,'default'=>1], 'Account Referred By'
			)->addColumn(
				'reviews_impovement', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['nullable'=>false,'default'=>1], 'Reviews & Impovement'
			)->addColumn(
				'subcription_to_newsletter', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11, ['nullable'=>false,'default'=>1], 'Subcriptionto Newsletter'
			)->addColumn(
				'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [], 'Account Created At'
			)->setComment('Account Table');
		$connection->createTable($table);

		$installer->endSetup();
	}
}
