<?php

declare(strict_types=1);

namespace SapientPro\Core\Setup\Patch\Data;

use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class InitializingThePacker implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private EavSetupFactory $eavSetupFactory;

    /**
     * @var CustomerSetupFactory
     */
    private CustomerSetupFactory $customerSetupFactory;

    /**
     * @var SetFactory
     */
    private SetFactory $attributeSetFactory;

    /**
     * AddPinnedPosSourceAttribute constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param CustomerSetupFactory $customerSetupFactory
     * @param SetFactory $attributeSetFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        CustomerSetupFactory $customerSetupFactory,
        SetFactory $attributeSetFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * Apply patch
     *
     * @return void
     */
    public function apply()
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->startSetup();

        $connection->update(
            $connection->getTableName('sales_order'),
            ['packer_id' => new \Zend_Db_Expr('cashier_id')],
            'packer_id IS NULL'
        );

        $connection->update(
            $connection->getTableName('sales_order_grid'),
            ['packer_id' => new \Zend_Db_Expr('cashier_id')],
            'packer_id IS NULL'
        );

        $connection->endSetup();
    }

    /**
     * Get array of patches that have to be executed
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Get array of patches that have to be executed
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * Revert path
     *
     * @throws LocalizedException
     */
    public function revert()
    {
        throw new LocalizedException(__('Irreversible migration'));
    }
}
