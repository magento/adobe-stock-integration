<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockStub\Plugin;

use Magento\AdobeStockImage\Model\Storage\Save;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Asset\Repository as AssetRepository;

/**
 * Change URL to the file path to emulate file download.
 */
class ChangeUrlToFilePath
{
    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var AssetRepository
     */
    private $assetRepository;

    /**
     * @param AssetRepository $assetRepository
     * @param DirectoryList $directoryList
     */
    public function __construct(AssetRepository $assetRepository, DirectoryList $directoryList)
    {
        $this->assetRepository = $assetRepository;
        $this->directoryList = $directoryList;
    }

    /**
     * @param Save $subject
     * @param string $imageUrl
     * @param string $destinationPath
     *
     * @return array
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function beforeExecute(
        Save $subject,
        string $imageUrl,
        string $destinationPath
    ) {
        $directoryPath = $this->directoryList->getPath(DirectoryList::STATIC_VIEW);
        $savedImage = substr($imageUrl, -6);
        $image = $this->assetRepository->createAsset('Magento_AdobeStockStub::images/' . $savedImage);
        $imagePath = $directoryPath.'/'.$image->getPath();

        return [$imagePath, $destinationPath];
    }
}

