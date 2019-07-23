<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Api\AssetRepository;

use Magento\AdobeStockAsset\Model\ResourceModel\Asset\CollectionFactory;
use Magento\AdobeStockAsset\Model\ResourceModel\Asset\Collection;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Testing delete asset web api
 */
class DeleteTest extends WebapiAbstract
{
    const SERVICE_NAME = 'adobeStockAssetRepositoryV1';
    const SERVICE_VERSION = 'V1';
    const RESOURCE_PATH = '/V1/adobestock/asset';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Collection
     */
    private $assetCollectionFactory;

    /**
     * Set up
     *
     * @return void
     */
    public function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->assetCollectionFactory = $this->objectManager->get(CollectionFactory::class);
    }

    /**
     * @magentoApiDataFixture assetFixtureProvider
     */
    public function testDelete()
    {
        $response = $this->deleteAsset($this->getAssetId());
        $this->assertSame([], $response);
    }

    /**
     * Delete Adobe Stock Asset
     *
     * @param int $assetId
     * @return array|bool|float|int|string
     */
    private function deleteAsset(int $assetId)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . DIRECTORY_SEPARATOR . $assetId,
                'httpMethod'   => Request::HTTP_METHOD_DELETE,
            ],
            'soap' => [
                'service'        => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'opertion'       => self::SERVICE_NAME . 'DeleteById',
            ],
        ];

        return (TESTS_WEB_API_ADAPTER === self::ADAPTER_SOAP) ?
            $this->_webApiCall($serviceInfo, [[AssetInterface::ID => $assetId]])
            : $this->_webApiCall($serviceInfo);
    }

    public static function assetFixtureProvider()
    {
        require __DIR__ . '/../../_files/asset.php';
    }

    /**
     * Get asset id
     *
     * @return int
     */
    private function getAssetId(): int
    {
        /** @var Collection $collection */
        $collection = $this->assetCollectionFactory->create();
        /** @var AssetInterface $asset */
        $asset = $collection->getLastItem();

        return (int) $asset->getId();
    }
}
