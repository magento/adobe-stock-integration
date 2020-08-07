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
 * Image preview column
 */
class ImagePreview extends Column
{
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $url
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $url,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->url = $url;
    }

    /**
     * @inheritdoc
     */
    public function prepare(): void
    {
        parent::prepare();
        $this->setData(
            'config',
            array_replace_recursive(
                (array) $this->getData('config'),
                [
                    'downloadImagePreviewUrl' => $this->url->getUrl('adobe_stock/preview/download'),
                    'licenseAndDownloadUrl' => $this->url->getUrl('adobe_stock/license/license'),
                    'saveLicensedAndDownloadUrl' => $this->url->getUrl('adobe_stock/license/saveLicensed'),
                    'confirmationUrl' => $this->url->getUrl('adobe_stock/license/confirmation'),
                    'relatedImagesUrl' => $this->url->getUrl('adobe_stock/preview/relatedimages'),
                    'getMediaGalleryAsset' => $this->url->getUrl('adobe_stock/asset/getmediagalleryasset'),
                    'buyCreditsUrl' => 'https://stock.adobe.com/',
                    'imageEditDetailsUrl' => $this->url->getUrl('media_gallery/image/details')
                ]
            )
        );
    }
}
