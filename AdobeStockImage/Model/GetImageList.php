<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\AdobeStockClientApi\Api\ClientInterface;

/**
 * Class GetImageList
 */
class GetImageList implements GetImageListInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * GetImageList constructor.
     * @param ClientInterface $client
     */
    public function __construct(
        ClientInterface $client
    ) {
        $this->client = $client;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function execute(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        return $this->client->search($searchCriteria);
    }
}
