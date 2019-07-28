<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Preview
 */
class Preview extends Column
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Preview constructor.
     *
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface       $urlBuilder
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->context = $context;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Set download image preview URL.
     */
    public function prepare(): void
    {
        $config = $this->getData('config');
        $config['downloadImagePreviewUrl'] = $this->buildDownloadPreviewUrl();
        $this->setData('config', $config);

        parent::prepare();
    }

    /**
     * Build the preview image download.
     *
     * @return string
     */
    private function buildDownloadPreviewUrl(): string
    {
        return $this->urlBuilder->getUrl('adobe_stock_image/preview/download');
    }
}
