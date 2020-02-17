<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model\Storage;

use Magento\Cms\Model\Wysiwyg\Images\Storage;
use Magento\Framework\Exception\CouldNotDeleteException;
use Psr\Log\LoggerInterface;

/**
 * Remove images from the filesystem
 */
class Delete
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Storage
     */
    private $storage;

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @param Storage $storage
     */
    public function __construct(
        LoggerInterface $logger,
        Storage $storage
    ) {
        $this->logger = $logger;
        $this->storage = $storage;
    }

    /**
     * Deletes the existing file
     *
     * @param string $path
     * @throws CouldNotDeleteException
     */
    public function execute(string $path): void
    {
        try {
            $this->storage->deleteFile($this->storage->getCmsWysiwygImages()->getStorageRoot() . $path);
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $message = __('Failed to delete the image: %error', ['error' => $exception->getMessage()]);
            throw new CouldNotDeleteException($message, $exception);
        }
    }
}
