<?php

namespace Rokanthemes\Customaddress\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
   private $customerSetupFactory;

   public function __construct(\Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory)
   {
       $this->customerSetupFactory = $customerSetupFactory;
   }

   public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
   {
       $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
       if (version_compare($context->getVersion(), '1.0.1') < 0) {
           $customerSetup->addAttribute('customer_address', 'gender_field', [
               'label' => 'Gender',
               'input' => 'select',
               'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
               'source' => 'Rokanthemes\Customaddress\Model\Attribute\Source\Gender',
               'required' => false,
               'position' => 90,
               'visible' => true,
               'system' => false,
               'is_used_in_grid' => false,
               'is_visible_in_grid' => false,
               'is_filterable_in_grid' => false,
               'is_searchable_in_grid' => false,
               'frontend_input' => 'hidden',
               'backend' => ''
           ]);

              $attribute=$customerSetup->getEavConfig()
                ->getAttribute('customer_address','gender_field')                                  
                ->addData(['used_in_forms' => [
                   'adminhtml_customer_address',
                   'adminhtml_customer',
                   'customer_address_edit',
                   'customer_register_address',
                   'customer_address',
                  ]
                ]);
           $attribute->save();
           
           $customerSetup->addAttribute('customer_address', 'email_field', [
               'label' => 'Email',
               'input' => 'text',
               'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
               'source' => '',
               'required' => false,
               'position' => 99,
               'visible' => true,
               'system' => false,
               'is_used_in_grid' => false,
               'is_visible_in_grid' => false,
               'is_filterable_in_grid' => false,
               'is_searchable_in_grid' => false,
               'frontend_input' => 'hidden',
               'backend' => ''
           ]);

              $attribute=$customerSetup->getEavConfig()
                ->getAttribute('customer_address','email_field')                                  
                ->addData(['used_in_forms' => [
                   'adminhtml_customer_address',
                   'adminhtml_customer',
                   'customer_address_edit',
                   'customer_register_address',
                   'customer_address',
                  ]
                ]);
           $attribute->save();
       }
   }
}
