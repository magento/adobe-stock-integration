<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\Components;

use Magento\AdobeStockAssetApi\Api\Data\MediaTypeInterface;
use Magento\AdobeStockAssetApi\Api\Data\MediaTypeInterfaceFactory;
use Magento\Framework\Exception\IntegrationException;
use Psr\Log\LoggerInterface;

/**
 * Class MediaTypeComponentFactory
 */
class MediaTypeComponentFactory
{
    /**
     * @var MediaTypeInterfaceFactory
     */
    private $mediaTypeFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * MediaTypeComponentFactory constructor.
     *
     * @param MediaTypeInterfaceFactory $mediaTypeFactory
     * @param LoggerInterface           $logger
     */
    public function __construct(MediaTypeInterfaceFactory $mediaTypeFactory, LoggerInterface $logger)
    {
        $this->mediaTypeFactory = $mediaTypeFactory;
        $this->logger = $logger;
    }

    /**
     * Generate media type asset component.
     *
     * @param int    $adobeId
     * @param string $name
     *
     * @return MediaTypeInterface
     * @throws IntegrationException
     */
    public function create(int $adobeId, string $name = ''): MediaTypeInterface
    {
        try {
            $mediaType = $this->mediaTypeFactory->create();
            $mediaType->setAdobeId($adobeId);
            $mediaType->setName($name);

            return $mediaType;
        } catch (\Exception $exception) {
            $message = __('Create media type asset component failed: %1', $exception->getMessage());
            $this->logger->critical($message);
            throw new IntegrationException($message, $exception);
        }
    }
}
