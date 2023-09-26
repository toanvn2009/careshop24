<?php


namespace Rokanthemes\BoughtViewed\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (!$installer->tableExists('product_bought_viewed')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('product_bought_viewed')) 
                ->addColumn('entity_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true
                ], 'Id')
				->addColumn(
					'product_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					11,
					[ 'nullable' => false],
					'Product Id'
				)
				->addColumn(
					'ip_address',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					150,
					[ 'nullable' => true],
					'Ip Address'
				)
				->addColumn(
					'created_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					225,
					[ 'nullable' => true],
					'Created At'
				)
                ->setComment('Product Bought Viewed');

            $installer->getConnection()->createTable($table); 
        }

        $installer->endSetup();
    }
}
