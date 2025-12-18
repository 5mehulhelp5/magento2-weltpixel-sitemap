<?php
namespace WeltPixel\Sitemap\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

Class MetaRobots extends AbstractFieldArray
{

    const META_ROBOTS_PATH = 'weltpixel_sitemap/general/meta_robots_options';

    /**
     * @var \WeltPixel\Sitemap\Block\Adminhtml\Form\Field\View\Element\Select
     */
    protected $_selectRenderer;

    protected function _prepareToRender()
    {
        $this->addColumn('route', ['label' => __('Route Path'), 'class' => 'required-entry admin__control-text']);
        $this->addColumn('meta_robots', [
            'label' => __('Meta Robots Value'),
            'size' => 100,
            'class' => 'required-entry admin__control-text',
            'renderer' => $this->_getMetaRobotOptionsRenderer()
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Meta Robots');
    }

    /**
     * @return \Magento\Framework\View\Element\Html\Select
     */
    protected function _getMetaRobotOptionsRenderer()
    {
        if (!$this->_selectRenderer) {
            $this->_selectRenderer = $this->getLayout()->createBlock(
                \WeltPixel\Sitemap\Block\Adminhtml\Form\Field\View\Element\Select::class
            );
            $this->_selectRenderer->setClass('required-entry admin__control-text');
            $this->_selectRenderer->setOptions($this->_getMetaRobotsOptions());
        }

        return $this->_selectRenderer;
    }

    /**
     * @return array[]
     */
    protected function _getMetaRobotsOptions()
    {
        return [
            ['value' => 'INDEX,FOLLOW', 'label' => 'INDEX, FOLLOW'],
            ['value' => 'NOINDEX,FOLLOW', 'label' => 'NOINDEX, FOLLOW'],
            ['value' => 'INDEX,NOFOLLOW', 'label' => 'INDEX, NOFOLLOW'],
            ['value' => 'NOINDEX,NOFOLLOW', 'label' => 'NOINDEX, NOFOLLOW']
        ];
    }
}
