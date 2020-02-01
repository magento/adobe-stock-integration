<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Plugin;

use Magento\MediaGalleryApi\Model\Keyword\Command\SaveAssetKeywordsInterface;
use Magento\MediaGalleryUi\Model\UpdateAssetKeywordsInGrid as Service;
use Magento\MediaGalleryUiApi\Api\ConfigInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Run update asset keywords for the enhanced media gallery grid
 */
class UpdateAssetKeywordsInGrid
{
    /**
     * @var Service
     */
    private $service;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * UpdateAssetKeywordsInGrid constructor.
     *
     * @param Service $service
     * @param ConfigInterface $config
     */
    public function __construct(Service $service, ConfigInterface $config)
    {
        $this->service = $service;
        $this->config = $config;
    }

    /**
     * Update the enhanced media gallery grid table when asset is saved with the keywords information
     *
     * @param SaveAssetKeywordsInterface $save
     * @param null $result
     * @param array $keywords
     * @param int $assetId
     *
     * @return void
     * @throws CouldNotSaveException
     */
    public function afterExecute(SaveAssetKeywordsInterface $save, $result, array $keywords, int $assetId): void
    {
        if ($this->config->isEnabled()) {
            $this->service->execute($keywords, $assetId);
        }
    }
}
