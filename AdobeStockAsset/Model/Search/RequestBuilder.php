<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockAsset\Model\Search;

use Magento\AdobeStockAsset\Model\Search\RequestBuilder\Binder;
use Magento\AdobeStockAssetApi\Api\SearchRequestBuilderInterface;
use Magento\AdobeStockAsset\Model\Request\ConfigInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Search\Request\NonExistingRequestNameException;
use Magento\AdobeStockAssetApi\Api\Data\SearchRequestInterfaceFactory;

/**
 * Class Builder
 * @package Magento\AdobeStockAsset\Model\Request
 * TODO: move constants to the request interface
 */
class RequestBuilder implements SearchRequestBuilderInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var array
     */
    private $data;

    /**
     * @var Binder
     */
    private $binder;

    /**
     * @var SearchRequestInterfaceFactory
     */
    private $requestFactory;

    /**
     * Builder constructor.
     * @param ConfigInterface $config
     * @param Binder $binder
     * @param SearchRequestInterfaceFactory $requestFactory
     */
    public function __construct(
        ConfigInterface $config,
        Binder $binder,
        SearchRequestInterfaceFactory $requestFactory
    ) {
        $this->config = $config;
        $this->binder = $binder;
        $this->requestFactory = $requestFactory;
    }

    /**
     * @inheritdoc
     */
    public function setName(string $name) : void
    {
        $this->data['requestName'] = $name;
    }

    /**
     * @inheritdoc
     */
    public function setSize(int $size): void
    {
        $this->data['size'] = $size;
    }

    /**
     * @inheritdoc
     */
    public function setOffset(int $offset): void
    {
        $this->data['offset'] = $offset;
    }

    /**
     * @inheritdoc
     */
    public function setSort(array $sort): void
    {
        $this->data['sort'] = $sort;
    }

    /**
     * @inheritdoc
     */
    public function setLocale(string $locale): void
    {
        $this->data['locale'] = $locale;
    }

    /**
     * @inheritdoc
     */
    public function bind(string $name, $value): void
    {
        $this->data['placeholder'][$name] = $value;
    }

    /**
     * @inheritdoc
     */
    public function create() : \Magento\AdobeStockAssetApi\Api\Data\SearchRequestInterface
    {
        if (!isset($this->data['requestName'])) {
            throw new \InvalidArgumentException("Request name not defined.");
        }
        $requestName = $this->data['requestName'];

        /** @var array $requestConfig */
        $requestConfig = $this->config->getRequestConfig($requestName);
        if ($requestConfig === null) {
            throw new NonExistingRequestNameException(new Phrase("Request name '%1' doesn't exist.", [$requestName]));
        }

        $requestConfig = $this->binder->bind($requestConfig, $this->data);

        return $this->convert($requestConfig);
    }

    /**
     * @param array $data
     * @return \Magento\AdobeStockAssetApi\Api\Data\SearchRequestInterface
     */
    private function convert(array $data) : \Magento\AdobeStockAssetApi\Api\Data\SearchRequestInterface
    {
        // TODO: Define request interface and refactor line below.
        return $this->requestFactory->create(['data' => $data]);
    }
}
