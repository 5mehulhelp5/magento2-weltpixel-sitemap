<?php

namespace WeltPixel\Sitemap\Model\ResourceModel\Cms;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Cms\Api\GetUtilityPageIdentifiersInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Page
 * @package WeltPixel\Sitemap\Model\ResourceModel\Cms
 */
class Page extends \Magento\Sitemap\Model\ResourceModel\Cms\Page
{
    /**
     * @var GetUtilityPageIdentifiersInterface
     */
    private $getUtilityPageIdentifiers;

    /**
     * Scope Config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Retrieve cms page collection array
     *
     * @param int $storeId
     * @return array
     */
    public function getCollection($storeId)
    {
        $entityMetadata = $this->metadataPool->getMetadata(PageInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $useCanonicalUrlForCmsPages = $this->isCanonicalUrlForCmsPages($storeId);

        $select = $this->getConnection()->select()->from(
            ['main_table' => $this->getMainTable()],
            [$this->getIdFieldName(), 'url' => 'identifier', 'updated_at' => 'update_time', 'wp_enable_canonical_url', 'wp_canonical_url', 'wp_use_canonical_url_in_sitemap']
        )->join(
            ['store_table' => $this->getTable('cms_page_store')],
            "main_table.{$linkField} = store_table.$linkField",
            []
        )->where(
            'main_table.is_active = 1'
        )->where(
            'main_table.identifier NOT IN (?)',
            $this->getUtilityPageIdentifiers()->execute()
        )->where(
            'store_table.store_id IN(?)',
            [0, $storeId]
        )->where(
            'main_table.exclude_from_sitemap = 0'
        );

        $pages = [];
        $query = $this->getConnection()->query($select);
        while ($row = $query->fetch()) {
            if ($useCanonicalUrlForCmsPages) {
                if ($row['wp_enable_canonical_url']) {
                    $canonicalUrl = $this->_parseCanonicalUrl($row['wp_canonical_url']);
                    if ($canonicalUrl) {
                        $row['url'] = $canonicalUrl;
                    }
                }
            } else {
                if ($row['wp_use_canonical_url_in_sitemap']) {
                    $canonicalUrl = $this->_parseCanonicalUrl($row['wp_canonical_url']);
                    if ($canonicalUrl) {
                        $row['url'] = $canonicalUrl;
                    }
                }
            }
            $page = $this->_prepareObject($row);
            $pages[$page->getId()] = $page;
        }

        return $pages;
    }

    /**
     * @return \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private function getUtilityPageIdentifiers()
    {
        if (!$this->getUtilityPageIdentifiers) {
            $this->getUtilityPageIdentifiers = ObjectManager::getInstance()->get(GetUtilityPageIdentifiersInterface::class);
        }
        return $this->getUtilityPageIdentifiers;
    }

    /**
     * @return \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private function getScopeConfig() {
        if (!$this->scopeConfig) {
            $this->scopeConfig = ObjectManager::getInstance()
                ->get(\Magento\Framework\App\Config\ScopeConfigInterface::class);
        }
        return $this->scopeConfig;
    }

    /**
     * @param $storeId
     * @return bool
     */
    private function isCanonicalUrlForCmsPages($storeId)
    {
        $scopeConfig = $this->getScopeConfig();
        return $scopeConfig->getValue(
            'weltpixel_sitemap/general/use_canonical_url_for_cmspage',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param string $canonicalUrl
     * @return string
     */
    private function _parseCanonicalUrl($canonicalUrl) {
        $canonicalUrl = $canonicalUrl ?? '';
        $urlParts = parse_url($canonicalUrl);
        $canonicalUrl = isset($urlParts['path']) ? $urlParts['path'] : '';
        $canonicalUrl .= isset($urlParts['query']) ? "?".$urlParts['query'] : '';

        return $canonicalUrl;
    }

}
