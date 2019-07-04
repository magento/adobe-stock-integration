<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Adobe Stock test connection block
 */
class TestConnection extends Field
{
    /**
     * @inheritdoc
     */
    protected $_template = 'Magento_AdobeStockAsset::system/config/testconnection.phtml';

    /**
     * Get the button and scripts contents
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData(
            [
                'button_label' => __($originalData['button_label']),
                'html_id' => $element->getHtmlId(),
                'ajax_url' => $this->_urlBuilder->getUrl(
                    'adobe_stock/system_config/testconnection',
                    [
                        'form_key' => $this->getFormKey()
                    ]
                )
            ]
        );

        return $this->_toHtml();
    }
}
