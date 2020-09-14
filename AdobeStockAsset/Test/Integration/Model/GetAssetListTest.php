<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Integration\Model;

use Magento\AdobeStockAssetApi\Api\GetAssetListInterface;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Provides integration test for the Adobe Stock GetAssetListInterface functionality.
 */
class GetAssetListTest extends TestCase
{
    /**
     * @var GetAssetListInterface
     */
    private $getAssetList;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        Bootstrap::getObjectManager()->configure([
            'preferences' => [
                ClientInterface::class => ClientMock::class
            ]
        ]);

        $this->getAssetList = Bootstrap::getObjectManager()->get(GetAssetListInterface::class);
    }

    /**
     * Test 'execute' method of GetAssetListInterface class
     *
     * @throws LocalizedException
     */
    public function testExecute(): void
    {
        $words = 'test';

        $filter = Bootstrap::getObjectManager()->get(FilterBuilder::class)
            ->setConditionType('fulltext')
            ->setField('words')
            ->setValue($words)
            ->create();
        $searchCriteria = Bootstrap::getObjectManager()->get(SearchCriteriaBuilder::class)
            ->addFilter($filter)
            ->create();

        /** @var SearchResultInterface $searchResults */
        $searchResults = $this->getAssetList->execute($searchCriteria);

        $this->assertInstanceOf(SearchResultInterface::class, $searchResults);
        $this->assertEquals(1, $searchResults->getTotalCount());
        $this->assertCount(1, array_values($searchResults->getItems()));
    }
}
