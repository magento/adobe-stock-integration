<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Test\Unit\Controller\Adminhtml\License;

use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\AdobeStockImageAdminUi\Controller\Adminhtml\License\License;
use Magento\AdobeStockImageApi\Api\SaveLicensedImageInterface;
use Magento\Backend\App\Action\Context as ActionContext;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Phrase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Test for the controller responsible for licensing an Adobe Stock image
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
     * @var SaveLicensedImageInterface|MockObject
     */
    private $saveLicensedImageMock;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->clientInterfaceMock = $this->createMock(ClientInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->contextMock = $this->createMock(ActionContext::class);
        $this->contextMock = $this->createPartialMock(ActionContext::class, ['getRequest', 'getResultFactory']);
        $this->saveLicensedImageMock = $this->createMock(SaveLicensedImageInterface::class);
        $this->requestMock = $this->createMock(RequestInterface::class);
        $this->resultFactoryMock = $this->createMock(ResultFactory::class);
        $this->jsonObject = $this->createMock(Json::class);

        $this->contextMock->expects($this->once())
            ->method('getResultFactory')
            ->willReturn($this->resultFactoryMock);
        $this->contextMock->expects($this->once())
            ->method('getRequest')
            ->willReturn($this->requestMock);
        $this->requestMock->expects($this->once())
            ->method('getParams')
            ->willReturn(
                [
                    'media_id' => 283415387,
                    'destination_path' => 'destination_path'
                ]
            );
        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with('json')
            ->willReturn($this->jsonObject);

        $this->sut = (new ObjectManager($this))->getObject(
            License::class,
            [
                'context' => $this->contextMock,
                'client' => $this->clientInterfaceMock,
                'saveLicensedImage' => $this->saveLicensedImageMock,
                'logger' => $this->loggerMock,
            ]
        );
    }

    /**
     * Test if the image is licensed and downloaded successfully
     */
    public function testExecute(): void
    {
        $result = [
            'success' => true,
            'message' => new Phrase('The image was licensed and saved successfully.')
        ];

        $mediaId = 283415387;
        $destinationPath = 'destination_path';

        $this->saveLicensedImageMock->expects($this->once())
            ->method('execute')
            ->with($mediaId, $destinationPath);
        $this->clientInterfaceMock->expects($this->once())
            ->method('licenseImage')
            ->with($mediaId);

        $this->jsonObject->expects($this->once())
            ->method('setHttpResponseCode')
            ->with(200);
        $this->jsonObject->expects($this->once())
            ->method('setData')
            ->with($this->equalTo($result));

        $this->loggerMock->expects($this->never())->method('critical');

        $this->sut->execute();
    }

    /**
     * Testing the behavior if some exceptions are thrown
     *
     * @dataProvider exceptionsDataProvider
     *
     * @param \Exception $exception
     * @param int $responseCode
     * @param array $result
     * @throws NotFoundException
     */
    public function testNotFoundAsset(\Exception $exception, int $responseCode, array $result): void
    {
        $mediaId = 283415387;

        $this->clientInterfaceMock->expects($this->once())
            ->method('licenseImage')
            ->with($mediaId)
            ->willThrowException($exception);
        $this->jsonObject->expects($this->once())
            ->method('setHttpResponseCode')
            ->with($responseCode);
        $this->jsonObject->expects($this->once())
            ->method('setData')
            ->with($this->equalTo($result));

        if ($responseCode === 500) {
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
                400,
                [
                    'success' => false,
                    'is_licensed' => false,
                    'message' => 'Requested image doesn\'t exists.'
                ]
            ],
            "Test the thrown exception if the asset couldn't be licensed or downloaded" => [
                new \Exception('Failed to save the image.'),
                500,
                [
                    'success' => false,
                    'is_licensed' => false,
                    'message' => 'An error occurred on attempt to license and save the image.'
                ]
            ]
        ];
    }
}
