<?php
namespace WeltPixel\Sitemap\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class AddMetaTags
 * @package WeltPixel\Sitemap\Observer
 */
class AddMetaTags implements ObserverInterface
{
    const PATH_FILTERED_PRODUCT_LISTINGS_ROBOTS = 'weltpixel_sitemap/general/filtered_product_listings_robots';
    const PATH_FILTERED_PRODUCT_LISTINGS_SEARCH_ROBOTS = 'weltpixel_sitemap/general/filtered_product_listings_search_results_robots';
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $_pageConfig;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \WeltPixel\Sitemap\Model\IndexFollowBuilder
     */
    protected $_indexFollowBuilder;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManagwer;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $_serializer;

    /**
     * AddMetaTags constructor.
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \WeltPixel\Sitemap\Model\IndexFollowBuilder $indexFollowBuilder
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    public function __construct(
        \Magento\Framework\View\Page\Config $pageConfig,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\View\LayoutInterface $layout,
        \WeltPixel\Sitemap\Model\IndexFollowBuilder $indexFollowBuilder,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Serialize\Serializer\Json $serializer
    ) {
        $this->_pageConfig = $pageConfig;
        $this->_request = $request;
        $this->_layout = $layout;
        $this->_indexFollowBuilder = $indexFollowBuilder;
        $this->_registry = $registry;
        $this->_storeManagwer = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_serializer = $serializer;
    }

    public function execute(Observer $observer)
    {
        $fullActionName = $this->_request->getFullActionName();
        $requestPath = $this->_request->getModuleName() .
            DIRECTORY_SEPARATOR . $this->_request->getControllerName() .
            DIRECTORY_SEPARATOR . $this->_request->getActionName();
        $baseUrl = $this->_storeManagwer->getStore()->getBaseUrl();

        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $observer->getLayout();

        $metaRobotsOptionsSerialized = $this->_scopeConfig->getValue(\WeltPixel\Sitemap\Block\Adminhtml\Form\Field\MetaRobots::META_ROBOTS_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (isset($metaRobotsOptionsSerialized) && strlen($metaRobotsOptionsSerialized)) {
            try {
                $metaRobotsOptions = $this->_serializer->unserialize($metaRobotsOptionsSerialized);
            } catch (\Exception $e) {
                $metaRobotsOptions = [];
            }
            foreach ($metaRobotsOptions as $metaRobotsOption) {
                if ($metaRobotsOption['route'] == $requestPath) {
                    $this->_pageConfig->setRobots($metaRobotsOption['meta_robots']);
                }
           }
        }


        switch ($fullActionName) {
            case 'cms_index_index':
            case 'cms_page_view':
            case 'cms_noroute_index':
                $page = $this->_layout->getBlock('cms_page');
                if ($page) $page = $page->getPage();
                if ($page && $page->getData('wp_enable_index_follow')) {
                    $indexValue = $page->getData('wp_index_value');
                    $followValue = $page->getData('wp_follow_value');
                    $indexFollowValue = $this->_indexFollowBuilder->getIndexFollowValue($indexValue, $followValue);
                    $this->_pageConfig->setRobots($indexFollowValue);
                }
                if ($page && $page->getData('wp_enable_canonical_url')) {
                    $canonicalUrl = $page->getData('wp_canonical_url');
                    $this->_pageConfig->addRemotePageAsset(
                        $canonicalUrl,
                        'canonical',
                        ['attributes' => ['rel' => 'canonical']]
                    );
                }
                break;
            case 'catalog_category_view':
                $currentCategory = $this->_registry->registry('current_category');
                $productListBlock = $layout->getBlock('category.products.list');
                $listingFiltersSelected = false;
                if ($currentCategory && $productListBlock && ($productListBlock instanceof \Magento\Catalog\Block\Product\ListProduct)) {
                    $state = $productListBlock->getLayer()
                        ->getState();

                    if ($state->getFilters()) {
                        $listingFiltersSelected = true;
                    }
                }
                if ($listingFiltersSelected) {
                    $robotsValue = $this->_scopeConfig->getValue(self::PATH_FILTERED_PRODUCT_LISTINGS_SEARCH_ROBOTS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                    $this->_pageConfig->setRobots($robotsValue);
                }
                elseif ($currentCategory && $currentCategory->getData('wp_enable_index_follow')) {
                    $indexValue = $currentCategory->getData('wp_index_value');
                    $followValue = $currentCategory->getData('wp_follow_value');
                    $indexFollowValue = $this->_indexFollowBuilder->getIndexFollowValue($indexValue, $followValue);
                    $this->_pageConfig->setRobots($indexFollowValue);
                }
                if ($currentCategory) {
                    $canonicalUrl = $currentCategory->getData('wp_canonical_url');
                    $infiniteScrollCanonicalEnabled = $this->_scopeConfig->getValue('weltpixel_infinite_scroll/advanced/prev_next', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) ?? false;
                    if (!$infiniteScrollCanonicalEnabled && $currentCategory->getData('wp_enable_canonical_url') && $canonicalUrl) {
                        $this->_pageConfig->addRemotePageAsset(
                            $canonicalUrl,
                            'canonical',
                            ['attributes' => ['rel' => 'canonical']]
                        );
                    }
                }
                break;
            case 'catalogsearch_result_index':
                $searchProductListBlock = $layout->getBlock('search_result_list');
                if ($searchProductListBlock && ($searchProductListBlock instanceof \Magento\Catalog\Block\Product\ListProduct)) {
                    $state = $searchProductListBlock->getLayer()
                        ->getState();

                    if ($state->getFilters()) {
                        $robotsValue = $this->_scopeConfig->getValue(self::PATH_FILTERED_PRODUCT_LISTINGS_ROBOTS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                        $this->_pageConfig->setRobots($robotsValue);
                    }
                }
                break;
            case 'catalog_product_view':
                $currentProduct = $this->_registry->registry('current_product');
                if ($currentProduct && $currentProduct->getData('wp_enable_index_follow')) {
                    $indexValue = $currentProduct->getData('wp_index_value');
                    $followValue = $currentProduct->getData('wp_follow_value');
                    $indexFollowValue = $this->_indexFollowBuilder->getIndexFollowValue($indexValue, $followValue);
                    $this->_pageConfig->setRobots($indexFollowValue);
                }
                if ($currentProduct) {
                    $canonicalUrl = $currentProduct->getData('wp_canonical_url');
                    if ($currentProduct->getData('wp_enable_canonical_url') && $canonicalUrl) {
                        $urlOptions = parse_url($canonicalUrl);
                        if (!isset($urlOptions['scheme'])) {
                            $canonicalUrl = $baseUrl . $canonicalUrl;
                        }
                        $this->_pageConfig->addRemotePageAsset(
                            $canonicalUrl,
                            'canonical',
                            ['attributes' => ['rel' => 'canonical']]
                        );
                    }
                }
                break;
        }
    }
}
