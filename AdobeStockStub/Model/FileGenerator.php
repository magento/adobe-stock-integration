<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Model;

use function PHPUnit\Framework\isEmpty;

/**
 * Generate a stub Adobe Stock API Asset file object based on the stub parameters.
 */
class FileGenerator
{
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

    private function getStubFile(): array
    {
        return [
            'id' => rand(1, 999),
            'comp_url' => 'https//adobe.stock.stub',
            'thumbnail_240_url' => 'https//adobe.stock.stub/'. rand(1, 240),
            'width' => rand(1, 100),
            'height' => rand(1, 100),
            'thumbnail_500_url' => 'https//adobe.stock.stub/'. rand(1, 500),
            'title' => 'Adobe Stock Stub file',
            'creator_id' => rand(1, 999),
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
                1 => ['name' => 'stub keyword 1'],
                2 => ['name' => 'stub keyword 1'],
                3 => ['name' => 'stub keyword 1'],
                4 => ['name' => 'stub keyword 1'],
            ],
            'media_type_id' => 1,
            'content_type' => 'image/jpeg',
            'details_url' => 'https//adobe.stock.stub/details',
            'premium_level_id' => 0,
        ];
    }
}
