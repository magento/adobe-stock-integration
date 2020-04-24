<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model\AssetDetailsProvider;

use Magento\Framework\Exception\IntegrationException;
use Magento\MediaContentApi\Api\GetContentByAssetIdsInterface;
use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\MediaGalleryUi\Model\AssetDetailsProviderInterface;

/**
 * Provide asset updated at date time
 */
class UpdatedAt implements AssetDetailsProviderInterface
{
    /**
     * Date format
     */
    private const DATE_FORMAT = 'd/m/Y, g:i A';

    /**
     * Provide asset updated at date time
     *
     * @param AssetInterface $asset
     * @return array
     * @throws \Exception
     */
    public function execute(AssetInterface $asset): array
    {
        return [
            'title' => __('Modified'),
            'value' => $this->formatDate($asset->getUpdatedAt())
        ];
    }

    /**
     * Format date to standard format
     *
     * @param string $date
     * @return string
     * @throws \Exception
     */
    private function formatDate(string $date): string
    {
        $dateTime = new \DateTime($date);

        return $dateTime->format(self::DATE_FORMAT);
    }
}
