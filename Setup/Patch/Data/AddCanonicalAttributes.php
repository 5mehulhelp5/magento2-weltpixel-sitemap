<?php
namespace WeltPixel\Sitemap\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Setup\CategorySetupFactory;

class AddCanonicalAttributes implements DataPatchInterface, PatchVersionInterface
{

    /**
     * Category setup factory
     *
     * @var CategorySetupFactory
     */
    private $catalogSetupFactory;

    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CategorySetupFactory $categorySetupFactory
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->catalogSetupFactory = $categorySetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $setup = $this->moduleDataSetup;
        $this->moduleDataSetup->startSetup();

        /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
        $catalogSetup = $this->catalogSetupFactory->create(['setup' => $setup]);
        $enableCanonicalUrlAttribute = 'wp_enable_canonical_url';
        $canonicalUrlAttribute = 'wp_canonical_url';

        $catalogSetup->addAttribute(Category::ENTITY, $enableCanonicalUrlAttribute, [
            'type' => 'int',
            'label' => 'Enable Canonical Url',
            'input' => 'select',
            'required' => false,
            'sort_order' => 25,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'wysiwyg_enabled' => false,
            'is_html_allowed_on_front' => false,
            'group' => 'WeltPixel Options',
            'default' => 0,
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean'
        ]);
        $catalogSetup->addAttribute(Category::ENTITY, $canonicalUrlAttribute, [
            'type' => 'varchar',
            'label' => 'Canonical Url',
            'input' => 'text',
            'required' => false,
            'sort_order' => 26,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'wysiwyg_enabled' => false,
            'is_html_allowed_on_front' => false,
            'group' => 'WeltPixel Options',
            'default' => ''
        ]);
        $catalogSetup->addAttribute(Product::ENTITY, $enableCanonicalUrlAttribute, [
            'type' => 'int',
            'label' => 'Enable Canonical Url',
            'input' => 'select',
            'required' => false,
            'sort_order' => 25,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'wysiwyg_enabled' => false,
            'is_html_allowed_on_front' => false,
            'group' => 'WeltPixel Options',
            'default' => 0,
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean'
        ]);
        $catalogSetup->addAttribute(Product::ENTITY, $canonicalUrlAttribute, [
            'type' => 'varchar',
            'label' => 'Canonical Url',
            'input' => 'text',
            'required' => false,
            'sort_order' => 26,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'wysiwyg_enabled' => false,
            'is_html_allowed_on_front' => false,
            'group' => 'WeltPixel Options',
            'default' => ''
        ]);

        $this->moduleDataSetup->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '1.0.5';
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [
            AddFollowAttributeData::class
        ];
    }
}
