<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Controller\Adminhtml\Preview;

use Magento\AdobeStockImageApi\Api\GetRelatedImagesInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\AuthenticationException;

/**
 * Controller providing related images (same model and same series) for the provided Adobe Stock asset id
 */
class RelatedImages extends Action implements HttpGetActionInterface
{
    private const HTTP_OK = 200;
    private const HTTP_INTERNAL_ERROR = 500;
    private const IMAGE_ID = 'image_id';
    private const LIMIT = 'limit';

    /**
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_AdobeStockImageAdminUi::save_preview_images';

    /**
     * @var GetRelatedImagesInterface
     */
    private $getRelatedImages;

    /**
     * RelatedImages constructor.
     *
     * @param Action\Context $context
     * @param GetRelatedImagesInterface $getRelatedImages
     */
    public function __construct(
        Action\Context $context,
        GetRelatedImagesInterface $getRelatedImages
    ) {
        parent::__construct($context);
        $this->getRelatedImages = $getRelatedImages;
    }
    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $params = $this->getRequest()->getParams();
            $imageId = (int) $params[self::IMAGE_ID];
            $limit = (int) ($params[self::LIMIT] ?? 4);
            $relatedImages = $this->getRelatedImages->execute($imageId, $limit);

            $responseCode = self::HTTP_OK;
            $responseContent = [
                'success' => true,
                'message' => __('Get related images finished successfully'),
                'result' => $relatedImages
            ];
        } catch (AuthenticationException $exception) {
            $responseCode = self::HTTP_INTERNAL_ERROR;
            $responseContent = [
                'success' => false,
                'message' => __(
                    'Failed to authenticate to Adobe Stock API. <br> Please correct the API credentials in '
                    . '<a href="%url">Configuration → System → Adobe Stock Integration.</a>',
                    [
                        'url' => $this->getUrl(
                            'adminhtml/system_config/edit',
                            [
                                'section' => 'system',
                                '_fragment' => 'system_adobe_stock_integration-link'
                            ]
                        )
                    ]
                )
            ];
        } catch (\Exception $exception) {
            $responseCode = self::HTTP_INTERNAL_ERROR;
            $responseContent = [
                'success' => false,
                'message' => __('An error occurred on attempt to fetch related images.'),
            ];
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setHttpResponseCode($responseCode);
        $resultJson->setData($responseContent);

        return $resultJson;
    }
}
