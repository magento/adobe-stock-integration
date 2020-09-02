<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Test\Integration\Model;

use Magento\AdobeStockClient\Model\Client;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\IntegrationException;

class ClientMock extends Client
{
    /**
     * Search for assets
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultInterface
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws IntegrationException
     */
    public function search(SearchCriteriaInterface $searchCriteria): SearchResultInterface
    {
        return parent::search($searchCriteria);
    }
}
