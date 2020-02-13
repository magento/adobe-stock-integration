<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Plugin;

use Magento\MediaGalleryApi\Model\Keyword\Command\SaveAssetKeywordsInterface;
use Magento\MediaGalleryUi\Model\UpdateAssetKeywordsInGrid as Service;
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
     * UpdateAssetKeywordsInGrid constructor.
     *
     * @param Service $service
     */
    public function __construct(Service $service)
    {
        $this->service = $service;
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
        $this->service->execute($keywords, $assetId);
    }
}
