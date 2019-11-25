<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImage\Model\Storage;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Filesystem;
use Psr\Log\LoggerInterface;

/**
 * Class Delete
 */
class Delete
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Storage constructor.
     * @param Filesystem $filesystem
     * @param LoggerInterface $logger
     */
    public function __construct(
        Filesystem $filesystem,
        LoggerInterface $logger
    ) {
        $this->filesystem = $filesystem;
        $this->logger = $logger;
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
            $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
            if ($mediaDirectory->isFile($path)) {
                $mediaDirectory->delete($path);
            }
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
            $message = __('Failed to delete the image: %error', ['error' => $exception->getMessage()]);
            throw new CouldNotDeleteException($message, $exception);
        }
    }
}
