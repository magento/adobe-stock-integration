<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Test\Unit\Controller\Adminhtml\Preview;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\AdobeStockImageAdminUi\Controller\Adminhtml\Preview\Series;
use Psr\Log\LoggerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Controller\Result\Json;
use Magento\Backend\App\Action\Context as ActionContext;
use Magento\Framework\Exception\IntegrationException;
use Magento\AdobeStockImage\Model\GetImageSeries;

/**
 * Series test.
 */
class SeriesTest extends TestCase
{

    /**
     * @var MockObject|LoggerInterface $logger
     */
    private $logger;

    /**
     * @var MockObject|ActionContext $context
     */
    private $context;

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
     * @var Series $series
     */
    private $series;

    /**
     * @var MockObject|GetImageSeries $getImageSeries
     */
    private $getImageSeries;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {

        $this->logger = $this->createMock(LoggerInterface::class);
        $this->getImageSeries = $this->createMock(GetImageSeries::class);
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

        $this->jsonObject = $this->getMockBuilder(Json::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultFactory->expects($this->once())->method('create')->with('json')->willReturn($this->jsonObject);
        $this->request->expects($this->once())
            ->method('getParams')
            ->willReturn(
                [
                    'serie_id' => "283415387",
                    'limit' => 4,
                    'is_ajax' => "true"
                ]
            );
        $this->series = new Series(
            $this->context,
            $this->getImageSeries,
            $this->logger
        );
    }

    /**
     * Verify that image series loaded.
     */
    public function testExecute()
    {
        $series = [
            'type' => 'series',
            'series' =>
                [
                    [
                        'id' => 283415387,
                        'title' => 'Old and worn work gloves on large American flag - Labor day background',
                        'thumbnail_url' => 'https://t4.ftcdn.net/jpg/02/83/41/53/240_F_a62iA2YYVG49yo2n.jpg'
                    ]
                ]
        ];
        $result = [
            'success' => true,
            'message' => new Phrase('Get image series finished successfully'),
            'result' => $series
        ];
        $this->getImageSeries->expects($this->once())->method('execute')->willReturn($series);
        $this->jsonObject->expects($this->once())->method('setHttpResponseCode')->with(200);
        $this->jsonObject->expects($this->once())->method('setData')
            ->with($this->equalTo($result));
        $this->series->execute();
    }

    /**
     * Verify that image series with exception
     */
    public function testExecuteWithException()
    {
        $result = [
            'success' => false,
            'message' => __('An error occurred while getting image series. Contact support.'),
        ];
        $this->getImageSeries->expects($this->once())
            ->method('execute')
            ->willThrowException(new IntegrationException(new Phrase('Error')));
        $this->jsonObject->expects($this->once())->method('setHttpResponseCode')->with(500);
        $this->jsonObject->expects($this->once())->method('setData')
            ->with($this->equalTo($result));
        $this->series->execute();
    }
}
