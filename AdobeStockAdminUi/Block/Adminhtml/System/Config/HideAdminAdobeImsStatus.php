<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAdminUi\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Hide Admin Adobe IMS status in admin system config
 */
class HideAdminAdobeImsStatus extends Field
{
    /**
     * @inheritdoc
     */
    public function render(AbstractElement $element): string
    {
        $html = '<td class="label"><label for="' .
            $element->getHtmlId() . '"><span' .
            $this->_renderScopeLabel($element) . '>' .
            $element->getLabel() .
            '</span></label></td>';
        $html .= $this->_renderValue($element);
        $html .= '<td class=""></td>';

        return $this->_decorateRowHtml($element, $html);
    }

    /**
     * Hide Admin Adobe IMS enabled/disabled status
     *
     * @param AbstractElement $element
     * @param string $html
     * @return string
     */
    protected function _decorateRowHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element, $html)
    {
        $style = ' style="display: none"';

        return '<tr id="row_' . $element->getHtmlId() . '"' . $style .'>' . $html . '</tr>';
    }
}
