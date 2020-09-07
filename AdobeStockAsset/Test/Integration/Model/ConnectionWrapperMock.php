<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Integration\Model;

use AdobeStock\Api\Models\StockFile;
use AdobeStock\Api\Response\SearchFiles as SearchFilesResponse;
use Magento\AdobeStockClient\Model\ConnectionWrapper;

class ConnectionWrapperMock extends ConnectionWrapper
{
    public function getNextResponse(): SearchFilesResponse
    {
        $response = new SearchFilesResponse();
        $response->setFiles($this->getStockFiles());
        $response->setNbResults(3);
        return $response;
    }

    /**
     * Result files.
     *
     * @return StockFile[]
     */
    private function getStockFiles(): array
    {
        $stockFilesData = [
            [
                'id' => 1,
                'comp_url' => 'https://test.url/1',
                'thumbnail_240_url' => 'https://test.url/1',
                'width' => 110,
                'height' => 210,
                'some_bool_param' => false,
                'some_nullable_param' => null,
                'category' => [
                    'id' => 1,
                    'N
                    name' => 'Test'
                ]
            ],
            [
                'id' => 2,
                'comp_url' => 'https://test.url/2',
                'thumbnail_240_url' => 'https://test.url/2',
                'width' => 120,
                'height' => 220,
                'some_bool_params' => false,
                'some_nullable_param' => 1,
                'category' => [
                    'id' => 1,
                    'N
                    name' => 'Test'
                ]
            ],
            [
                'id' => 3,
                'comp_url' => 'https://test.url/3',
                'thumbnail_240_url' => 'https://test.url/3',
                'width' => 130,
                'height' => 230,
                'some_bool_params' => true,
                'some_nullable_param' => 2,
                'category' => [
                    'id' => 1,
                    'N
                    name' => 'Test'
                ]
            ],
        ];

        $stockFiles = [];
        foreach ($stockFilesData as $stockFileData) {
            $stockFiles[] = new StockFile($stockFileData);
        }

        return $stockFiles;
    }
}
