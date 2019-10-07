<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeMediaGallery\Model\Keyword\Command;

use Magento\AdobeMediaGalleryApi\Api\Data\KeywordInterface;
use Magento\AdobeMediaGalleryApi\Api\Data\KeywordInterfaceFactory;
use Magento\AdobeMediaGalleryApi\Model\Keyword\Command\GetAssetKeywordsInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NotFoundException;

/**
 * ClassGetAssetKeywords
 */
class GetAssetKeywords implements GetAssetKeywordsInterface
{
    private const TABLE_KEYWORD = 'adobe_media_gallery_keyword';

    private const TABLE_ASSET_KEYWORD = 'adobe_media_gallery_asset_keyword';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var KeywordInterfaceFactory
     */
    private $keywordFactory;

    /**
     * GetAssetKeywords constructor.
     *
     * @param ResourceConnection      $resourceConnection
     * @param KeywordInterfaceFactory $keywordFactory
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        KeywordInterfaceFactory $keywordFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->keywordFactory = $keywordFactory;
    }

    /**
     * Get asset related keywords.
     *
     * @param int $assetId
     *
     * @return KeywordInterface[]
     * @throws NotFoundException
     */
    public function execute(int $assetId): array
    {
        try {
            $connection = $this->resourceConnection->getConnection();

            $select = $connection->select()
                ->from(['k' => self::TABLE_KEYWORD])
                ->join(['ak' => self::TABLE_ASSET_KEYWORD], 'k.id = ak.keyword_id')
                ->where('ak.asset_id = ?', $assetId);
            $data = $connection->query($select)->fetchAll();

            $keywords = [];
            foreach ($data as $keywordData) {
                $keyword = $this->keywordFactory->create();
                $keyword->setId($keywordData[KeywordInterface::ID]);
                $keyword->setKeyword($keywordData[KeywordInterface::KEYWORD]);
                $keywords[] = $keyword;
            }

            return $keywords;
        } catch (\Exception $exception) {
            $message = __('An error occurred during get asset keywords: %1', $exception->getMessage());
            throw new NotFoundException($message, $exception);
        }
    }
}
