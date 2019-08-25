<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\Framework\Exception\SerializationException;

/**
 * Class ImageSeriesSerialize
 */
class ImageSeriesSerialize
{
    /**
     * Serialize image series data.
     *
     * @param AssetInterface[] $series
     *
     * @return array
     * @throws SerializationException
     */
    public function execute(array $series): array
    {
        $data = [];
        try {
            /** @var AssetInterface $asset */
            foreach ($series as $asset) {
                $item['id'] = $asset->getId();
                $item['title'] = $asset->getTitle();
                $item['thumbnail_url'] = $asset->getThumbnailUrl();
                $data[] = $item;
            }

            $result = [
                'type' => 'series',
                'assets' => $data,
            ];

            return $result;
        } catch (\Exception $exception) {
            $message = __('An error occurred during image series serialization: %s', $exception->getMessage());
            throw new SerializationException($message);
        }
    }
}
