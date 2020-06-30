<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model;

use Magento\Framework\View\Asset\Repository as AssetRepository;

/**
 * Generate a stub Adobe Stock API Asset file.
 */
class FileGenerator
{
    /**
     * @var AssetRepository
     */
    private $assetRepository;

    /**
     * @param AssetRepository $assetRepository
     */
    public function __construct(
        AssetRepository $assetRepository
    ) {
        $this->assetRepository = $assetRepository;
    }

    /**
     * Generate stub asset Files.
     *
     * @param int $totalCount
     *
     * @return array
     */
    public function generate(int $totalCount): array
    {
        return [
            'nb_results' => 424242,
            'files' => array_map(
                function () {
                    return $this->getAssetData();
                },
                array_fill(0, $totalCount, [])
            )
        ];
    }

    /**
     * Prepare the etalon File data for the Response.
     *
     * @return array
     */
    private function getAssetData(): array
    {
        return [
            'id' => rand(1, 150),
            'comp_url' => 'https//adobe.stock.stub',
            'thumbnail_240_url' => $this->getImageUrl(),
            'width' => rand(1, 10),
            'height' => rand(1, 10),
            'thumbnail_500_url' => $this->getImageUrl(),
            'title' => 'Adobe Stock Stub file',
            'creator_id' => rand(1, 10),
            'creator_name' => 'Adobe Stock file creator name',
            'creation_date' => '2020-03-11 12:50:05.542333',
            'country_name' => 'Adobe Stock Stub file country name',
            'category' => [
                'id' => 1,
                'name' => 'Stub category',
                'link' => null,
            ],
            'keywords' => [
                0 => ['name' => 'stub keyword 1'],
                1 => ['name' => 'stub keyword 2'],
                2 => ['name' => 'stub keyword 3'],
                3 => ['name' => 'stub keyword 4'],
                4 => ['name' => 'stub keyword 5'],
            ],
            'media_type_id' => 1,
            'content_type' => 'image/png',
            'details_url' => $this->getImageUrl(),
            'premium_level_id' => 0,
        ];
    }

    /**
     * @return string
     */
    private function getImageUrl(): string
    {
        return $this->assetRepository->getUrl('Magento_AdobeStockStub::images/' . rand(1, 3) . '.png');
    }
}
