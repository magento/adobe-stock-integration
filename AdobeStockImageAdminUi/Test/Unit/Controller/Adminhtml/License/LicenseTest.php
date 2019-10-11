<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Test\Unit\Controller\Adminhtml\License;

use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockAssetApi\Api\GetAssetByIdInterface;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\AdobeStockImageAdminUi\Controller\Adminhtml\License\License;
use Magento\AdobeStockImageApi\Api\SaveImageInterface;
use Magento\Backend\App\Action\Context as ActionContext;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Phrase;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * License test.
 */
class LicenseTest extends TestCase
{
    /**
     * @var License
     */
    private $sut;

    /**
     * @var ClientInterface|MockObject
     */
    private $clientInterfaceMock;

    /**
     * @var LoggerInterface|MockObject
     */
    private $loggerMock;

    /**
     * @var ActionContext|MockObject
     */
    private $contextMock;

    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    /**
     * @var ResultFactory|MockObject
     */
    private $resultFactoryMock;

    /**
     * @var Json|MockObject
     */
    private $jsonObject;

    /**
     * @var SaveImageInterface|MockObject
     */
    private $saveImageMock;

    /**
     * @var GetAssetByIdInterface|MockObject
     */
    private $getAssetByIdMock;

    /**
     * @var AssetInterface|MockObject
     */
    private $assetMock;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->clientInterfaceMock = $this->createMock(ClientInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->contextMock = $this->createMock(ActionContext::class);
        $this->contextMock = $this->createPartialMock(ActionContext::class, ['getRequest', 'getResultFactory']);
        $this->saveImageMock = $this->createMock(SaveImageInterface::class);
        $this->getAssetByIdMock = $this->createMock(GetAssetByIdInterface::class);
        $this->assetMock = $this->createMock(AssetInterface::class);
        $this->requestMock = $this->createMock(RequestInterface::class);
        $this->resultFactoryMock = $this->getMockBuilder(ResultFactory::class)->disableOriginalConstructor()
            ->setMethods(['create'])->getMock();
        $this->jsonObject = $this->getMockBuilder(Json::class)->disableOriginalConstructor()->getMock();

        $this->contextMock->expects($this->once())->method('getResultFactory')->willReturn($this->resultFactoryMock);
        $this->contextMock->expects($this->once())->method('getRequest')->willReturn($this->requestMock);
        $this->requestMock->expects($this->once())->method('getParams')->willReturn(
            [
                'media_id' => 283415387,
                'destination_path' => 'destination_path'
            ]
        );
        $this->resultFactoryMock->expects($this->once())->method('create')->with('json')->willReturn($this->jsonObject);

        $this->sut = new License(
            $this->contextMock,
            $this->clientInterfaceMock,
            $this->saveImageMock,
            $this->loggerMock,
            $this->getAssetByIdMock
        );
    }

    /**
     * Test if the image is licensed and downloaded successfully
     */
    public function testExecute()
    {
        $result = [
            'success' => true,
            'message' => new Phrase('You have successfully licensed and downloaded the image.')
        ];

        $mediaId = 283415387;
        $isLicensed = 1;
        $destinationPath = 'destination_path';
        $imageUrl = 'http://image_url.jpg';
        $this->getAssetByIdMock->expects($this->once())->method('execute')->with($mediaId)
            ->willReturn($this->assetMock);
        $this->clientInterfaceMock->expects($this->once())->method('getImageDownloadUrl')->willReturn($imageUrl);
        $this->assetMock->expects($this->once())->method('setUrl')->with($imageUrl);
        $this->assetMock->expects($this->once())->method('setIsLicensed')->with($isLicensed);
        $this->saveImageMock->expects($this->once())->method('execute')->with($this->assetMock, $destinationPath);
        $this->clientInterfaceMock->expects($this->once())->method('licenseImage')->with($mediaId);

        $this->jsonObject->expects($this->once())->method('setHttpResponseCode')->with(License::HTTP_OK);
        $this->jsonObject->expects($this->once())->method('setData')
            ->with($this->equalTo($result));

        $this->loggerMock->expects($this->never())->method('critical');

        $this->sut->execute();
    }

    /**
     * Testing the behavior if some exceptions are thrown
     *
     * @dataProvider exceptionsDataProvider
     *
     * @param $exception
     * @param int $responseCode
     * @param array $result
     */
    public function testNotFoundAsset($exception, int $responseCode, array $result)
    {
        $mediaId = 283415387;

        $this->getAssetByIdMock->expects($this->once())->method('execute')->with($mediaId)
            ->willThrowException($exception);
        $this->jsonObject->expects($this->once())->method('setHttpResponseCode')->with($responseCode);
        $this->jsonObject->expects($this->once())->method('setData')->with($this->equalTo($result));

        if ($responseCode === License::HTTP_INTERNAL_ERROR) {
            $this->loggerMock->expects($this->once())->method('critical');
        }

        $this->sut->execute();
    }

    /**
     * Providing thrown exceptions
     *
     * @return array
     */
    public function exceptionsDataProvider(): array
    {
        return [
            "Test the thrown exception if the asset couldn't be found" => [
                new NotFoundException(new Phrase('Requested image doesn\'t exists.')),
                License::HTTP_BAD_REQUEST,
                [
                    'success' => false,
                    'message' => new Phrase('Image not found. Could not be saved.')
                ]
            ],
            "Test the thrown exception if the asset couldn't be licensed or downloaded" => [
                new LocalizedException(new Phrase('Failed to save the image.')),
                License::HTTP_INTERNAL_ERROR,
                [
                    'success' => false,
                    'message' => new Phrase('An error occurred while image license and download. Contact support.')
                ]
            ]
        ];
    }
}
