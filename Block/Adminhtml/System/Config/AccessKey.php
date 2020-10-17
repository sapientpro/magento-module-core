<?php
/**
 * SapientPro
 *
 * @category    SapientPro
 * @package     SapientPro_Core
 * @author      SapientPro Team <info@sapient.pro >
 * @copyright   Copyright © 2009-2019 SapientPro (https://sapient.pro)
 */

namespace SapientPro\Core\Block\Adminhtml\System\Config;

/**
 * Class AccessKey
 * @package SapientPro\Core\Block\Adminhtml\System\Config
 */
class AccessKey extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Render text
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = '';
        if ($element->getComment()) {
            $html .= '<div class="section-config" style="margin: auto; padding: 10px; height: 1500px;">' . $element->getComment() . '</div>';
        }

        return $html;
    }

    /**
     * Return element html
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }
}
