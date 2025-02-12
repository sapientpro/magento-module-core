<?php

declare(strict_types=1);

namespace SapientPro\Core\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Customer\Model\Customer;

class MoveAttributesToStaffTab implements DataPatchInterface
{
    private $moduleDataSetup;
    private $eavSetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);


        $attributes = [
            'allow_pos_terminal',
            'is_allow_pos_discount',
            'allowed_pos_sources',
            'default_pos_source'
        ];

        foreach ($attributes as $attributeCode) {
            $eavSetup->updateAttribute(Customer::ENTITY, $attributeCode, [
                'visible' => false,
                'is_visible' => false,
            ]);
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public static function getDependencies() { return []; }
    public function getAliases() { return []; }
}
