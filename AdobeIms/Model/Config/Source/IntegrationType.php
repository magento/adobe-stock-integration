<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdobeIms\Model\Config\Source;

class IntegrationType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'openid,creative_sdk', 'label' => __('Creative SDK ')],
            ['value' => 'openid', 'label' => __('Adobe Stock')]
        ];
    }
}
