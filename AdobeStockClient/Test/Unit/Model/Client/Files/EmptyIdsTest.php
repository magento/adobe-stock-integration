<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockClient\Test\Unit\Model\Client\Files;

use Magento\AdobeIms\Model\Config as ImsConfig;
use Magento\AdobeImsApi\Api\GetAccessTokenInterface;
use Magento\AdobeStockClient\Model\Client\Files;
use Magento\AdobeStockClient\Model\Config as ClientConfig;
use Magento\Framework\Exception\IntegrationException;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test case when ids sent to the files service is an empty array
 */
class EmptyIdsTest extends TestCase
{
    /**
     * @var Files|MockObject
     */
    private $files;

    /**
     * Prepare test objects.
     */
    protected function setUp(): void
    {
        $imsConfigMock = $this->createMock(ImsConfig::class);
        $clientConfigMock = $this->createMock(ClientConfig::class);
        $localeResolverMock = $this->createMock(Resolver::class);
        $getAccessTokenMock = $this->createMock(GetAccessTokenInterface::class);
        $jsonMock = $this->createMock(Json::class);
        $curlFactoryMock = $this->createMock(\Magento\Framework\HTTP\Client\CurlFactory::class);

        $this->files = (new ObjectManager($this))->getObject(
            Files::class,
            [
                'imsConfig' => $imsConfigMock,
                'clientConfig' => $clientConfigMock,
                'localeResolver' => $localeResolverMock,
                'getAccessToken' => $getAccessTokenMock,
                'curlFactory' => $curlFactoryMock,
                'json' => $jsonMock,
            ]
        );
    }

    /**
     * Execute test for case when ids is an empty array
     */
    public function testIdsIsEmptyArray(): void
    {
        $this->expectException(IntegrationException::class);
        $this->files->execute([], []);
    }
}
