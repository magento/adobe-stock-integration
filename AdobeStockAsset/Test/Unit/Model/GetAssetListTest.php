<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Unit\Model;

use Magento\AdobeStockAsset\Model\GetAssetList;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for GetAssetList service
 */
class GetAssetListTest extends TestCase
{
    /**
     * @var GetAssetList
     */
    private $model;

    /**
     * @var ClientInterface|MockObject
     */
    private $clientMock;

    /**
     * @var UrlInterface|MockObject
     */
    private $urlMock;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(ClientInterface::class);
        $this->urlMock = $this->createMock(UrlInterface::class);

        $this->model = (new ObjectManager($this))->getObject(
            GetAssetList::class,
            [
                'client'              => $this->clientMock,
                'url'                 => $this->urlMock
            ]
        );
    }

    /**
     * Test execute method
     * @throws LocalizedException
     */
    public function testExecute(): void
    {
        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);
        $documentSearchResults = $this->createMock(SearchResultInterface::class);

        $this->clientMock->expects($this->once())
            ->method('search')
            ->with($searchCriteriaMock)
            ->willReturn($documentSearchResults);

        $this->assertEquals($documentSearchResults, $this->model->execute($searchCriteriaMock));
    }
}
