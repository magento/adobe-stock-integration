<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Ui\Component\Listing\Columns;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Store\Model\Store;

/**
 * Construct source item url based on image source
 */
class ConstructSourceItemUrl
{
    /**
     * @var AssetRepository
     */
    private $assetRepository;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var array
     */
    private $imageSource;

    /**
     * ConstructSourceItemUrl constructor.
     *
     * @param AssetRepository $assetRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param array $imageSource
     */
    public function __construct(
        AssetRepository $assetRepository,
        ScopeConfigInterface $scopeConfig,
        array $imageSource = []
    ) {
        $this->assetRepository = $assetRepository;
        $this->scopeConfig = $scopeConfig;
        $this->imageSource = $imageSource;
    }

    /**
     * Construct source icon url based on the source code matching
     *
     * @param string $sourceName
     *
     * @return string|null
     */
    public function execute(string $sourceName): ?string
    {
        $sourceIconUrl = null;
        if (!empty($this->imageSource) && isset($this->imageSource[$sourceName])) {
            $sourceIconUrl = $this->assetRepository->getUrlWithParams(
                $this->imageSource[$sourceName],
                ['_secure' => $this->getIsSecure()]
            );
        }

        return $sourceIconUrl;
    }

    /**
     * Check if store use secure connection
     *
     * @return bool
     */
    private function getIsSecure(): bool
    {
        return $this->scopeConfig->isSetFlag(Store::XML_PATH_SECURE_IN_ADMINHTML);
    }
}
