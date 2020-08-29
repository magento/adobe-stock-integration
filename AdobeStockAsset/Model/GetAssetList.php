<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAssetApi\Api\GetAssetListInterface;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * Service for getting asset list filtered by search criteria
 */
class GetAssetList implements GetAssetListInterface
{
    /**
     * @var AppendAttributes
     */
    private $appendAttributes;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * GetAssetList constructor.
     * @param ClientInterface $client
     * @param LoggerInterface $log
     * @param AppendAttributes $appendAttributes
     */
    public function __construct(
        ClientInterface $client,
        LoggerInterface $log,
        AppendAttributes $appendAttributes
    ) {
        $this->client = $client;
        $this->log = $log;
        $this->appendAttributes = $appendAttributes;
    }

    /**
     * @inheritdoc
     */
    public function execute(SearchCriteriaInterface $searchCriteria): SearchResultInterface
    {
        try {
            $searchResult = $this->client->search($searchCriteria);
            $this->appendAttributes->execute($searchResult);

            return $searchResult;
        } catch (AuthenticationException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->log->critical($exception);
            throw new LocalizedException(
                __('Cannot retrieve assets from Adobe Stock.'),
                $exception,
                $exception->getCode()
            );
        }
    }
}
