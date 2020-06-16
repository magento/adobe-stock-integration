<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model;

use Magento\Backend\Model\UrlInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use function PHPUnit\Framework\isEmpty;

/**
 * Generate a stub Adobe Stock API Asset file object based on the stub parameters.
 */
class FileGenerator
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * Generate an array of Adobe Stock stub files array based on the query.
     *
     * @param array $stubData
     * @param int $recordsCount
     *
     * @return array
     */
    public function generate(array $stubData, int $recordsCount): array
    {
        $files = [];
        $i = 0;
        do {
            $files[] = isEmpty($stubData) ? array_merge($this->getStubFile(), $stubData) : $this->getStubFile();
            $i++;
        } while ($recordsCount > $i);

        return $files;
    }

    /**
     * Generate base stub media file.
     *
     * @return array
     * @throws NoSuchEntityException
     */
    private function getStubFile(): array
    {
        /** @var Store $store */
        $store = $this->storeManager->getStore();
        $baseUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        return [
            'id' => rand(1, 150),
            'comp_url' => 'https//adobe.stock.stub',
            'thumbnail_240_url' => $baseUrl. 'images/stub-image.png',
            'width' => rand(1, 100),
            'height' => rand(1, 100),
            'thumbnail_500_url' => $baseUrl. 'images/stub-image.png',
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
            'details_url' => $baseUrl. 'images/stub-image.png',
            'premium_level_id' => 0,
        ];
    }
}
