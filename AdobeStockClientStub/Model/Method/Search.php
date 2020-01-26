<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClientStub\Model\Method;

use AdobeStock\Api\Response\SearchFiles;
use AdobeStock\Api\Response\SearchFiles as SearchFilesResponse;
use Magento\AdobeStockClient\Model\StockFileToDocument;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\SearchResultFactory;
use Magento\AdobeStockClientStub\Model\DataProvider\RawStockResponse;

/**
 * Provides a stub result for the search method of the AdobeStockClient
 */
class Search
{
    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var RawStockResponse
     */
    private $rawStockResponse;

    /**
     * @var StockFileToDocument
     */
    private $stockFileToDocument;

    /**
     * Search constructor.
     *
     * @param SearchResultFactory $searchResultFactory
     * @param RawStockResponse $rawStockResponse
     * @param StockFileToDocument $stockFileToDocument
     */
    public function __construct(
        SearchResultFactory $searchResultFactory,
        RawStockResponse $rawStockResponse,
        StockFileToDocument $stockFileToDocument
    ) {
        $this->searchResultFactory = $searchResultFactory;
        $this->rawStockResponse = $rawStockResponse;
        $this->stockFileToDocument = $stockFileToDocument;
    }

    /**
     * Return the Adobe Stock client search result stub data
     *
     * @return SearchResultInterface
     */
    public function execute(): SearchResultInterface
    {
        $responseStub = $this->generateResponseStub();
        $items = [];
        foreach ($responseStub->getFiles() as $file) {
            $items[] = $this->stockFileToDocument->convert($file);
        }
        $totalCount = $responseStub->getNbResults();

        $searchResult = $this->searchResultFactory->create();
        $searchResult->setItems($items);
        $searchResult->setTotalCount($totalCount);

        return $searchResult;
    }

    /**
     * Generate response stub
     *
     * @return SearchFiles
     */
    private function generateResponseStub(): SearchFilesResponse
    {
        $searchFilesResponse = new SearchFilesResponse();
        $responseArray = $this->rawStockResponse->getRawAdobeStockResponse();
        $searchFilesResponse->initializeResponse($responseArray);

        return $searchFilesResponse;
    }
}
