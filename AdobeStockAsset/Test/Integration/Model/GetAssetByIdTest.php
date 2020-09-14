<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Integration\Model;

use Magento\AdobeStockAssetApi\Api\GetAssetByIdInterface;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class GetAssetByIdTest extends TestCase
{
    /**
     * @var GetAssetByIdInterface
     */
    private $getAssetById;

    protected function setUp(): void
    {
        Bootstrap::getObjectManager()->configure([
            'preferences' => [
                ClientInterface::class => ClientMock::class
            ]
        ]);

        $this->getAssetById = Bootstrap::getObjectManager()->get(GetAssetByIdInterface::class);
    }

    public function testExecute(): void
    {
        /** @var DocumentInterface $searchResults */
        $searchResults = $this->getAssetById->execute(1);

        $this->assertInstanceOf(DocumentInterface::class, $searchResults);
        $this->assertNotEmpty($searchResults);
        $this->assertEquals(1, $searchResults->getId());
    }
}
