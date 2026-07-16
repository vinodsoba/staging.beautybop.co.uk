<?php

namespace BeautyFort\BeautyfortProductImport\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddBeautyfortRrpAttribute implements DataPatchInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;

    private EavSetupFactory $eavSetupFactory;

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

        $eavSetup = $this->eavSetupFactory->create([
            'setup' => $this->moduleDataSetup
        ]);

        // Prevent duplicate attribute if it already exists
        if ($eavSetup->getAttributeId(Product::ENTITY, 'beautyfort_rrp')) {
            $this->moduleDataSetup->getConnection()->endSetup();
            return;
        }

        $eavSetup->addAttribute(
            Product::ENTITY,
            'beautyfort_rrp',
            [
                'type' => 'decimal',
                'label' => 'BeautyFort RRP',

                'input' => 'price',

                'required' => false,
                'user_defined' => true,
                'visible' => true,

                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,

                'searchable' => false,
                'filterable' => false,
                'comparable' => false,

                'used_in_product_listing' => true,

                'visible_on_front' => false,

                'sort_order' => 999,

                'group' => 'General'
            ]
        );

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}