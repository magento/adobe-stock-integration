<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeMediaGallery\Model\Asset\Command;

use Magento\AdobeMediaGalleryApi\Api\Data\AssetInterface;
use Magento\AdobeMediaGalleryApi\Model\Asset\Command\SaveInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\CouldNotSaveException;
use Zend_Db;

/**
 * Class Save
 */
class Save implements SaveInterface
{
    private const TABLE_ADOBE_MEDIA_GALLERY = 'adobe_media_gallery';

    private const ID = 'id';

    private const PATH = 'path';

    private const TITLE = 'title';

    private const CONTENT_TYPE = 'content_type';

    private const WIDTH = 'width';

    private const HEIGHT = 'height';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * SaveAssetKeywords constructor.
     *
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Save media assets
     *
     * @param AssetInterface $asset
     *
     * @return int
     * @throws CouldNotSaveException
     */
    public function execute(AssetInterface $asset): int
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $tableName = $this->resourceConnection->getTableName(
                self::TABLE_ADOBE_MEDIA_GALLERY
            );

            $onDuplicateFields = [
                self::PATH,
                self::TITLE,
                self::CONTENT_TYPE,
                self::WIDTH,
                self::HEIGHT,
            ];

            $insertData = [
                self::PATH => $asset->getPath(),
                self::TITLE => $asset->getTitle(),
                self::CONTENT_TYPE => $asset->getContentType(),
                self::WIDTH => $asset->getWidth(),
                self::HEIGHT => $asset->getHeight(),
            ];

            $connection->insertOnDuplicate($tableName, $insertData, $onDuplicateFields);
            return $this->getMediaAssetId($asset);
        } catch (\Exception $exception) {
            $message = __('An error occurred during media asset save: %1', $exception->getMessage());
            throw new CouldNotSaveException($message, $exception);
        }
    }

    /**
     * Get saved asset id.
     *
     * @param AssetInterface $asset
     *
     * @return int
     */
    private function getMediaAssetId(AssetInterface $asset): int
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(['amg' => self::TABLE_ADOBE_MEDIA_GALLERY])
            ->where('amg.' . self::PATH . ' = ?', $asset->getPath())
            ->where('amg.' . self::TITLE . ' = ?', $asset->getTitle())
            ->where('amg.' . self::WIDTH . ' = ?', $asset->getWidth())
            ->where('amg.' . self::HEIGHT . ' = ?', $asset->getHeight())
            ->where('amg.' . self::CONTENT_TYPE . ' = ?', $asset->getContentType());

        $row = $connection->query($select)->fetch(Zend_Db::FETCH_ASSOC);

        return (int) $row[self::ID];
    }
}
