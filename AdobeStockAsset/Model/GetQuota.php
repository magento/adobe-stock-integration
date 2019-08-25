<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAssetApi\Api\Data\CreatorExtensionInterface;
use Magento\AdobeStockClientApi\Api\ClientInterface;

/**
 * Class GetQuota
 *
 * Service for retrieving license quota for the user with specified access token
 */
class GetQuota
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * GetQuota constructor.
     *
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Retrieves license quota for the provided content
     *
     * @param int $contentId
     * @param string $accessToken
     * @return int
     */
    public function execute(int $contentId, string $accessToken): int
    {
        return $this->client->getQuota($contentId, $accessToken);
    }
}
