<?php
namespace WeltPixel\Sitemap\Block\Adminhtml\Form\Field\View\Element;

Class Select extends  \Magento\Framework\View\Element\Html\Select
{
    /**
     * Render HTML
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _toHtml()
    {
        if (!$this->_beforeToHtml()) {
            return '';
        }

        $html = '<select name="' .
            $this->getInputName() .
            '" id="' .
            $this->getInputId() .
            '" class="' .
            $this->getClass() .
            '" title="' .
            $this->escapeHtml($this->getTitle()) .
            '" ' .
            $this->getExtraParams() .
            '>';

        $values = $this->getValue();
        if (!is_array($values)) {
            $values = (array)$values;
        }

        $isArrayOption = true;
        foreach ($this->getOptions() as $key => $option) {
            $optgroupName = '';
            if ($isArrayOption && is_array($option)) {
                $value = $option['value'];
                $label = (string)$option['label'];
                $optgroupName = isset($option['optgroup-name']) ? $option['optgroup-name'] : $label;
                $params = !empty($option['params']) ? $option['params'] : [];
            } else {
                $value = (string)$key;
                $label = (string)$option;
                $isArrayOption = false;
                $params = [];
            }

            if (is_array($value)) {
                $html .= '<optgroup label="' . $this->escapeHtml($label)
                    . '" data-optgroup-name="' . $this->escapeHtml($optgroupName) . '">';
                foreach ($value as $keyGroup => $optionGroup) {
                    if (!is_array($optionGroup)) {
                        $optionGroup = ['value' => $keyGroup, 'label' => $optionGroup];
                    }
                    $html .= $this->_optionToHtml($optionGroup, in_array($optionGroup['value'], $values));
                }
                $html .= '</optgroup>';
            } else {
                $html .= $this->_optionToHtml(
                    ['value' => $value, 'label' => $label, 'params' => $params],
                    in_array($value, $values)
                );
            }
        }
        $html .= '</select>';
        return $html;
    }

    /**
     * Alias for toHtml()
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->toHtml();
    }
}
