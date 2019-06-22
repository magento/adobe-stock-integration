<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdobeStockImageAdminUi\Block\Adminhtml;

/**
 *
 * Class AdobeStockImage
 *
 * @package Magento\AdobeStockImageAdminUi\Block\Adminhtml
 */
class AdobeStockImage extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_url;

    /**
     * AdobeStockImage constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\UrlInterface $url
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\UrlInterface $url,
        array $data = []
    ) {
        $this->_url = $url;
        parent::__construct($context, $data);
    }


    /**
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->_url->getUrl('mui/index/render', ['namespace' => 'adobe_stock_images_listing']);
    }
}
