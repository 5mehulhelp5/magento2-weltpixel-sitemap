<?php
namespace WeltPixel\Sitemap\Setup\Patch\Schema;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

class AddFollowColumns implements SchemaPatchInterface, PatchVersionInterface
{
    /**
     * @var SchemaSetupInterface $schemaSetup
     */
    private $schemaSetup;

    /**
     * @param SchemaSetupInterface $schemaSetup
     */
    public function __construct(SchemaSetupInterface $schemaSetup)
    {
        $this->schemaSetup = $schemaSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $setup = $this->schemaSetup;
        $this->schemaSetup->startSetup();

        $setup->getConnection()->addColumn(
            $setup->getTable('cms_page'),
            'wp_enable_index_follow',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Enable Index Follow',
                'default' => 0
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('cms_page'),
            'wp_index_value',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Index Value',
                'default' => 0
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('cms_page'),
            'wp_follow_value',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Follow Value',
                'default' => 0
            ]
        );


        $this->schemaSetup->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '1.0.1';
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
            AddSitemapTable::class
        ];
    }
}
