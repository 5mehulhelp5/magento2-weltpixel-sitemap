<?php
namespace WeltPixel\Sitemap\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Setup\CategorySetupFactory;

class AddFollowAttributes implements DataPatchInterface, PatchVersionInterface
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
        $enableIndexFollowAttribute = 'wp_enable_index_follow';
        $indexAttribute = 'wp_index_value';
        $followAttribute = 'wp_follow_value';

        $catalogSetup->addAttribute(Category::ENTITY, $enableIndexFollowAttribute, [
            'type' => 'int',
            'label' => 'Enable Index Follow',
            'input' => 'select',
            'required' => false,
            'sort_order' => 15,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'wysiwyg_enabled' => false,
            'is_html_allowed_on_front' => false,
            'group' => 'WeltPixel Options',
            'default' => 0,
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean'
        ]);
        $catalogSetup->addAttribute(Category::ENTITY, $indexAttribute, [
            'type' => 'int',
            'label' => 'Index Value',
            'input' => 'select',
            'required' => false,
            'sort_order' => 16,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'wysiwyg_enabled' => false,
            'is_html_allowed_on_front' => false,
            'group' => 'WeltPixel Options',
            'default' => 0,
            'source' => 'WeltPixel\Sitemap\Model\Attribute\Source\IndexValue'
        ]);
        $catalogSetup->addAttribute(Category::ENTITY, $followAttribute, [
            'type' => 'int',
            'label' => 'Follow Value',
            'input' => 'select',
            'required' => false,
            'sort_order' => 17,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'wysiwyg_enabled' => false,
            'is_html_allowed_on_front' => false,
            'group' => 'WeltPixel Options',
            'default' => 0,
            'source' => 'WeltPixel\Sitemap\Model\Attribute\Source\FollowValue'
        ]);
        $catalogSetup->addAttribute(Product::ENTITY, $enableIndexFollowAttribute, [
            'type' => 'int',
            'label' => 'Enable Index Follow',
            'input' => 'select',
            'required' => false,
            'sort_order' => 15,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'wysiwyg_enabled' => false,
            'is_html_allowed_on_front' => false,
            'group' => 'WeltPixel Options',
            'default' => 0,
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean'
        ]);
        $catalogSetup->addAttribute(Product::ENTITY, $indexAttribute, [
            'type' => 'int',
            'label' => 'Index Value',
            'input' => 'select',
            'required' => false,
            'sort_order' => 16,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'wysiwyg_enabled' => false,
            'is_html_allowed_on_front' => false,
            'group' => 'WeltPixel Options',
            'default' => 0,
            'source' => 'WeltPixel\Sitemap\Model\Attribute\Source\IndexValue'
        ]);
        $catalogSetup->addAttribute(Product::ENTITY, $followAttribute, [
            'type' => 'int',
            'label' => 'Follow Value',
            'input' => 'select',
            'required' => false,
            'sort_order' => 17,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'wysiwyg_enabled' => false,
            'is_html_allowed_on_front' => false,
            'group' => 'WeltPixel Options',
            'default' => 0,
            'source' => 'WeltPixel\Sitemap\Model\Attribute\Source\FollowValue'
        ]);
        $this->moduleDataSetup->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '1.0.2';
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
            AddSitemapAttributes::class
        ];
    }
}
