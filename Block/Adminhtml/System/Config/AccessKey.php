<?php
/**
 * SapientPro
 *
 * @category    SapientPro
 * @package     SapientPro_Core
 * @author      SapientPro Team <info@sapient.pro >
 * @copyright   Copyright © 2009-2022 SapientPro (https://sapient.pro)
 */

namespace SapientPro\Core\Block\Adminhtml\System\Config;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field;

class AccessKey extends Field
{
    /**
     * @throws LocalizedException
     */
    public function render(AbstractElement $element): string
    {
        $html = '';
        if ($element->getComment()) {
            $html .= '<div class="section-config" style="margin: auto; padding: 10px; height: 1500px;">' .
                $element->getComment() . '</div>';
        }

        return $html;
    }

    /**
     * Render block HTML
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        return $this->_toHtml();
    }
}
