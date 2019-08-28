<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Test\Unit\Controller\Adminhtml\Preview;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\AdobeStockImage\Model\SaveImagePreview;
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
     * @var MockObject|SaveImagePreview $saveImagePreview
     */
    private $saveImagePreview;

    /**
     * @var MockObject|LoggerInterface $logger
     */
    private $logger;

    /**
     * @var MockObject|ActionContext $context
     */
    private $context;

    /**
     * @var Download $download
     */
    private $download;

    /**
     * @var MockObject $request
     */
    private $request;

    /**
     * @var MockObject $resultFactory
     */
    private $resultFactory;

    /**
     * @var MockObject $jsonObject
     */
    private $jsonObject;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->saveImagePreview = $this->createMock(SaveImagePreview::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->context = $this->createMock(\Magento\Backend\App\Action\Context::class);
        $this->request = $this->getMockForAbstractClass(
            \Magento\Framework\App\RequestInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['getParams']
        );
        $this->context->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($this->request));
        $this->resultFactory = $this->getMockBuilder(\Magento\Framework\Controller\ResultFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->context->expects($this->once())
            ->method('getResultFactory')
            ->willReturn($this->resultFactory);
        $this->saveImagePreview->expects($this->once())->method('execute')->willReturn(null);
        $this->jsonObject = $this->getMockBuilder(Json::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultFactory->expects($this->once())->method('create')->with('json')->willReturn($this->jsonObject);
        $this->request->expects($this->once())
            ->method('getParams')
            ->willReturn(
                [
                'isAjax' => "true",
                'media_id' => 283415387,
                'destination_path' => "",
                'form_key' => "PyXOATf2fL9Y8iZf"
                ]
            );

        $this->download = new Download(
            $this->context,
            $this->saveImagePreview,
            $this->logger
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
        $this->saveImagePreview->method('execute')->willThrowException(new CouldNotSaveException(new Phrase('Error')));
        $this->jsonObject->expects($this->once())->method('setHttpResponseCode')->with(500);
        $this->jsonObject->expects($this->once())->method('setData')
            ->with($this->equalTo($result));
        $this->download->execute();
    }
}
