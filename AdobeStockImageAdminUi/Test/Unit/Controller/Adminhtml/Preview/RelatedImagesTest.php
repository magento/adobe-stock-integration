<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Test\Unit\Controller\Adminhtml\Preview;

use Magento\AdobeStockImageAdminUi\Controller\Adminhtml\Preview\RelatedImages;
use Magento\AdobeStockImageApi\Api\GetRelatedImagesInterface;
use Magento\Backend\App\Action\Context as ActionContext;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\IntegrationException;
use Magento\Framework\Phrase;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Test for controller providing Adobe Stock asset related images
 */
class RelatedImagesTest extends TestCase
{
    /**
     * @var MockObject|LoggerInterface
     */
    private $logger;

    /**
     * @var MockObject|ActionContext
     */
    private $context;

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
     * @var RelatedImages
     */
    private $relatedImages;

    /**
     * @var MockObject|GetRelatedImagesInterface
     */
    private $getRelatedImages;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->getRelatedImages = $this->createMock(GetRelatedImagesInterface::class);
        $this->context = $this->createMock(ActionContext::class);
        $this->request = $this->createMock(RequestInterface::class);
        $this->context->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($this->request));
        $this->resultFactory = $this->createMock(ResultFactory::class);
        $this->context->expects($this->once())
            ->method('getResultFactory')
            ->willReturn($this->resultFactory);

        $this->jsonObject = $this->createMock(Json::class);
        $this->resultFactory->expects($this->once())->method('create')->with('json')->willReturn($this->jsonObject);
        $this->request->expects($this->once())
            ->method('getParams')
            ->willReturn(
                [
                    'image_id' => "283415387",
                    'limit' => 4,
                    'is_ajax' => "true"
                ]
            );
        $this->relatedImages = new RelatedImages(
            $this->context,
            $this->getRelatedImages,
            $this->logger
        );
    }

    /**
     * Verify that image relatedImages loaded.
     */
    public function testExecute(): void
    {
        $relatedImages = [
            'same_model' => [
                    [
                        'id' => 283415387,
                        'title' => 'Old and worn work gloves on large American flag - Labor day background',
                        'thumbnail_url' => 'https://t4.ftcdn.net/jpg/02/83/41/53/240_F_a62iA2YYVG49yo2n.jpg'
                    ]
                ],
            'same_series' => [
                    [
                        'id' => 283415387,
                        'title' => 'Old and worn work gloves on large American flag - Labor day background',
                        'thumbnail_url' => 'https://t4.ftcdn.net/jpg/02/83/41/53/240_F_a62iA2YYVG49yo2n.jpg'
                    ]
                ]
        ];
        $result = [
            'success' => true,
            'message' => new Phrase('Get related images finished successfully'),
            'result' => $relatedImages
        ];
        $this->getRelatedImages->expects($this->once())->method('execute')->willReturn($relatedImages);
        $this->jsonObject->expects($this->once())->method('setHttpResponseCode')->with(200);
        $this->jsonObject->expects($this->once())->method('setData')
            ->with($this->equalTo($result));
        $this->relatedImages->execute();
    }

    /**
     * Verify that image relatedImages with exception
     */
    public function testExecuteWithException(): void
    {
        $result = [
            'success' => false,
            'message' => 'An error occurred on attempt to fetch related images.',
        ];
        $this->getRelatedImages->expects($this->once())
            ->method('execute')
            ->willThrowException(new IntegrationException(new Phrase('Error')));
        $this->jsonObject->expects($this->once())->method('setHttpResponseCode')->with(500);
        $this->jsonObject->expects($this->once())->method('setData')
            ->with($this->equalTo($result));
        $this->relatedImages->execute();
    }
}
