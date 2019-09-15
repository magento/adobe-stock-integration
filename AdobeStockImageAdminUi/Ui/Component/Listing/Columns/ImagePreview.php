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
 * Class ImagePreview
 */
class ImagePreview extends Column
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * ImagePreview constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        parent::prepare();

        $this->setData(
            'config',
            array_replace_recursive(
                (array)$this->getData('config'),
                [
                    'downloadImagePreviewUrl' => $this->urlBuilder->getUrl('adobe_stock/preview/download'),
                    'getQuotaUrl' => $this->urlBuilder->getUrl('adobe_stock/license/getquota'),
                    'imageSeriesUrl' => $this->urlBuilder->getUrl('adobe_stock/preview/series')
                ]
            )
        );
    }
}
