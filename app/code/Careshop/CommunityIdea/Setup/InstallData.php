<?php

namespace Careshop\CommunityIdea\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /** Add root category */
        $sampleTemplates = [
            'path'           => '1',
            'position'       => 0,
            'children_count' => 0,
            'level'          => 0,
            'name'           => 'ROOT',
            'url_key'        => 'root'
        ];

        $setup->getConnection()->insert($setup->getTable('community_category'), $sampleTemplates);

        $installer->endSetup();
    }
}
