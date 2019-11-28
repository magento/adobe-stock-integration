<?php

namespace Magento\MediaGalleryUi\Model;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Store\Model\StoreManagerInterface;
use FilesystemIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Exception;

class ImagesProvider
{
    /** @var DirectoryList */
    private $directoryList;

    /** @var StoreManagerInterface */
    private $storeManager;

    public function __construct(
        DirectoryList $directoryList,
        StoreManagerInterface $storeManager
    ) {
        $this->directoryList = $directoryList;
        $this->storeManager = $storeManager;
    }

    public function getImages(): array
    {
        $mediaRootPath = $this->directoryList->getPath(DirectoryList::MEDIA);
        $result = $this->readFiles($mediaRootPath);

        if (empty($result)) {
            throw new NoSuchEntityException(__('No images found!'));
        }

        return $result;
    }

    public function readFiles(string $path): array
    {
        $result = [];

        $flags = FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS;
        try {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, $flags),
                RecursiveIteratorIterator::CHILD_FIRST
            );

            $mediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
            $i = 1;

            /** @var FilesystemIterator $item */
            foreach ($iterator as $item) {
                $file = $item->getPath() . '/' . $item->getFileName();
                $image = preg_match("#\.(jpg|jpeg|gif|png)$# i", $file);
                if ($image) {
                    list($width, $height) = getimagesize($file);
                    $imageUrl = str_replace($path, $mediaUrl, $file);

                    $result['items'][] = [
                        'id_field_name' => 'id',
                        'id' => $i++,
                        'title' => $imageUrl,
                        'url' => $imageUrl,
                        'preview_url' => '',
                        'width' => $width,
                        'height' => $height
                    ];
                }
            }
        } catch (Exception $e) {
            throw new FileSystemException(new Phrase($e->getMessage()), $e);
        }

        return $result;
    }
}
