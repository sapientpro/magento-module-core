<?php

declare(strict_types=1);

namespace SapientPro\Core\Setup\Patch\Data;

use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;

class AddAllowPosTerminalAttribute implements DataPatchInterface, PatchRevertableInterface
{
    public const ATTRIBUTE_CODE = 'allow_pos_terminal';

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
     * AddAllowPosTerminalAttribute constructor.
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
     * Apply data patch
     *
     * @return void
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /**
         * @var $attributeSet Set
        */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        if (!$customerSetup->getAttribute(Customer::ENTITY, self::ATTRIBUTE_CODE)) {
            $customerSetup->addAttribute(
                Customer::ENTITY,
                self::ATTRIBUTE_CODE,
                [
                    'type' => 'int',
                    'label' => 'Allow POS Terminal',
                    'input' => 'boolean',
                    'source' => Boolean::class,
                    'required' => false,
                    'position' => 999,
                    'visible' => true,
                    'system' => false,
                    'is_used_in_grid' => true,
                    'is_visible_in_grid' => true,
                    'is_filterable_in_grid' => true,
                    'is_searchable_in_grid' => false,
                    'backend' => ''
                ]
            );

            $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, self::ATTRIBUTE_CODE);
            $attribute->addData(
                [
                    'used_in_forms' => ['adminhtml_customer']
                ]
            );

            $attribute->addData(
                [
                    'attribute_set_id' => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId
                ]
            );

            $attribute->save();
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Get dependencies that are executed prior to this
     *
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Reverse code that was applied
     *
     * @return array
     */
    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $customerSetup->removeAttribute(Customer::ENTITY, self::ATTRIBUTE_CODE);
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Get aliases (previous names) for the patch
     *
     * @return array
     */
    public function getAliases()
    {
        return [];
    }
}
