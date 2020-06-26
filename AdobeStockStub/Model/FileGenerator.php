<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model;

use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Generate a stub Adobe Stock API Asset file.
 */
class FileGenerator
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var AssetRepository
     */
    private $assetRepository;

    /**
     * @var string[][]
     */
    private $stubImages;

    /**
     * @param StoreManagerInterface $storeManager
     * @param AssetRepository $assetRepository
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        AssetRepository $assetRepository
    ) {
        $this->storeManager = $storeManager;
        $this->assetRepository = $assetRepository;
    }

    /**
     * Generate stub asset Files.
     *
     * @param int $filesAmount
     *
     * @return array
     */
    public function generate(int $filesAmount): array
    {
        $files = [];
        $iterator = 0;
        do {
            $file = $this->constructStubFileData($iterator);
            $files[] = $file;
            $iterator++;
        } while ($filesAmount > $iterator);

        return [
            'nb_results' => count($files),
            'files' => $files,
        ];
    }

    /**
     * Prepare the etalon File data for the Response.
     *
     * @param int $iterator
     *
     * @return array
     */
    private function constructStubFileData(int $iterator): array
    {
        $this->setStubImages();
        switch ($iterator) {
            case $iterator % 2:
                $stubImage = $this->stubImages[1];
                break;
            case $iterator % 3:
                $stubImage = $this->stubImages[2];
                break;
            default:
                $stubImage = $this->stubImages[0];
        }

        return [
            'id' => rand(1, 150),
            'comp_url' => 'https//adobe.stock.stub',
            'thumbnail_240_url' => $stubImage['url'],
            'width' => rand(1, 10),
            'height' => rand(1, 10),
            'thumbnail_500_url' => $stubImage['url'],
            'title' => $stubImage['title'],
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
            'details_url' => $stubImage['url'],
            'premium_level_id' => 0,
        ];
    }

    /**
     * Set stub images.
     */
    private function setStubImages()
    {
        if (empty($this->stubImages)) {
            $this->stubImages = [
                [
                    'title' => 'Adobe Stock Stub file 1',
                    'url' => $this->assetRepository->getUrl('Magento_AdobeStockStub::images/1.png'),
                ],
                [
                    'title' => 'Adobe Stock Stub file 2',
                    'url' => $this->assetRepository->getUrl('Magento_AdobeStockStub::images/2.png'),
                ],
                [
                    'title' => 'Adobe Stock Stub file 3',
                    'url' => $this->assetRepository->getUrl('Magento_AdobeStockStub::images/3.png'),
                ],
            ];
        }
    }
}
