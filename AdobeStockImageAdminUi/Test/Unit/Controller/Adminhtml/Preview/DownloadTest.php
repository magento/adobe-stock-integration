<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Test\Unit\Controller\Adminhtml\Preview;

use Magento\AdobeStockAssetApi\Api\GetAssetByIdInterface;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\AdobeStockImageApi\Api\SaveImageInterface;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\App\RequestInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Controller\Result\Json;
use Magento\Backend\App\Action\Context as ActionContext;
use Magento\AdobeStockImageAdminUi\Controller\Adminhtml\Preview\Download;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * DownloadTest
 */
class DownloadTest extends TestCase
{
    /**
     * @var MockObject|DocumentInterface
     */
    private $document;

    /**
     * @var MockObject|GetAssetByIdInterface
     */
    private $getAssetById;

    /**
     * @var MockObject|SaveImageInterface
     */
    private $saveImage;

    /**
     * @var MockObject|LoggerInterface
     */
    private $logger;

    /**
     * @var MockObject|ActionContext
     */
    private $context;

    /**
     * @var Download
     */
    private $download;

    /**
     * @var MockObject
     */
    private $request;

    /**
     * @var MockObject
     */
    private $resultFactory;

    /**
     * @var MockObject
     */
    private $jsonObject;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->saveImage = $this->getMockForAbstractClass(SaveImageInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->context = $this->createMock(\Magento\Backend\App\Action\Context::class);
        $this->getAssetById = $this->getMockForAbstractClass(GetAssetByIdInterface::class);
        $this->document = $this->createMock(DocumentInterface::class);

        $attribute = $this->createMock(AttributeInterface::class);
        $attribute->expects($this->once())
            ->method('getValue')
            ->willReturn('https://url');

        $this->document->expects($this->once())
            ->method('getCustomAttribute')
            ->willReturn($attribute);

        $this->request = $this->createMock(RequestInterface::class);
        $this->request->expects($this->once())
            ->method('getParams')
            ->willReturn(
                [
                    'isAjax' => 'true',
                    'media_id' => 283415387,
                    'destination_path' => '',
                    'form_key' => 'PyXOATf2fL9Y8iZf'
                ]
            );
        $this->getAssetById->expects($this->once())
            ->method('execute')
            ->willReturn($this->document);
        $this->context->expects($this->once())
            ->method('getRequest')
            ->willReturn($this->request);
        $this->resultFactory = $this->getMockBuilder(\Magento\Framework\Controller\ResultFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->context->expects($this->once())
            ->method('getResultFactory')
            ->willReturn($this->resultFactory);
        $this->saveImage->expects($this->once())->method('execute')->willReturn(null);
        $this->jsonObject = $this->getMockBuilder(Json::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultFactory->expects($this->once())->method('create')->with('json')->willReturn($this->jsonObject);

        $this->download = new Download(
            $this->context,
            $this->saveImage,
            $this->logger,
            $this->getAssetById
        );
    }

    /**
     * Verify that image can be downloaded
     */
    public function testExecute()
    {
        $result = ['success' => true, 'message' => new Phrase('You have successfully downloaded the image.')];
        $this->jsonObject->expects($this->once())->method('setHttpResponseCode')->with(200);
        $this->jsonObject->expects($this->once())->method('setData')
            ->with($this->equalTo($result));
        $this->download->execute();
    }

    /**
     * Verify that exception will throw is image not available.
     */
    public function testExecuteWithException()
    {
        $result = ['success' => false, 'message' => new Phrase('An error occurred while image download. Contact support.')];
        $this->saveImage->method('execute')->willThrowException(new CouldNotSaveException(new Phrase('Error')));
        $this->jsonObject->expects($this->once())->method('setHttpResponseCode')->with(500);
        $this->jsonObject->expects($this->once())->method('setData')
            ->with($this->equalTo($result));
        $this->download->execute();
    }
}
