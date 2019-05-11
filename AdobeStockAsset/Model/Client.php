<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdobeStockAsset\Model;

use AdobeStock\Api\Client\AdobeStock;
use AdobeStock\Api\Core\Constants;
use AdobeStock\Api\Models\SearchParameters;
use AdobeStock\Api\Request\SearchFiles as SearchFilesRequest;
use Magento\AdobeStockApi\Api\ClientInterface;
use Magento\AdobeStockApi\Api\Data\ConfigInterface;

/**
 * DataProvider for cms ui.
 */
class Client implements ClientInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * Client constructor.
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @return mixed
     */
    public function search()
    {
        // TODO THIS IS A STUB
        $results_columns = Constants::getResultColumns();
        $search_params = new SearchParameters();
        $search_params->setWords('tree')->setLimit(32)->setOffset(0);

        $result_column_array = [
            $results_columns['COMP_URL'],
            $results_columns['ID'],
            $results_columns['NB_RESULTS'],
        ];

        $request = new SearchFilesRequest();
        $request->setLocale('En_US');
        $request->setSearchParams($search_params);
        $request->setResultColumns($result_column_array);

        $client = $this->getClient()->searchFilesInitialize($request, $this->getAccessToken());

        $response = $client->getNextResponse();

        $result = ['count' => $response->getNbResults()];

        foreach ($response->getFiles() as $file) {
            $result['items'][] = [
                'id' => $file->id,
                'url' => $file->comp_url
            ];
        }

        return $result;
    }

    /**
     * @return null
     */
    private function getAccessToken()
    {
        return null;
    }

    /**
     * @return AdobeStock
     */
    private function getClient()
    {
        return new AdobeStock(
            $this->config->getApiKey(),
            $this->config->getProductName(),
            $this->config->getTargetEnvironment()
        );
    }
}
