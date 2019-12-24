<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Ui\Component\Listing\Columns;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Overlay column
 */
class Url extends Column
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Repository $assetRepository
     */
    private $assetRepository;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param StoreManagerInterface $storeManager
     * @param Repository $assetRepo
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        StoreManagerInterface $storeManager,
        Repository $assetRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->storeManager = $storeManager;
        $this->assetRepository = $assetRepository;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $mediaUrl . $item[$this->getData('name')];
            }
        }

        return $dataSource;
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
                    'adobeStockIconUrl' => $this->assetRepository->getUrl('Magento_MediaGalleryUi::images/Astock.png'),
                ]
            )
        );
    }
}
