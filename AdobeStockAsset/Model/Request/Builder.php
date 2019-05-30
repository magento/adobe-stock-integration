<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockAsset\Model\Request;

use Magento\AdobeStockAssetApi\Api\RequestBuilderInterface;
use Magento\AdobeStockAsset\Model\Request\Builder\ConfigInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Search\Request\NonExistingRequestNameException;
use Magento\AdobeStockAssetApi\Api\Data\RequestInterfaceFactory;

/**
 * Class Builder
 * @package Magento\AdobeStockAsset\Model\Request
 * TODO: move constants to the request interface
 */
class Builder implements RequestBuilderInterface
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
     * @var Builder\Binder
     */
    private $binder;

    /**
     * @var RequestInterfaceFactory
     */
    private $requestFactory;

    /**
     * Builder constructor.
     * @param ConfigInterface $config
     * @param Builder\Binder $binder
     * @param RequestInterfaceFactory $requestFactory
     */
    public function __construct(
        ConfigInterface $config,
        Builder\Binder $binder,
        RequestInterfaceFactory $requestFactory
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
    public function create() : \Magento\AdobeStockAssetApi\Api\Data\RequestInterface
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
     * @return \Magento\AdobeStockAssetApi\Api\Data\RequestInterface
     */
    private function convert(array $data) : \Magento\AdobeStockAssetApi\Api\Data\RequestInterface
    {
        // TODO: Define request interface and refactor line below.
        return $this->requestFactory->create(['data' => $data]);
    }
}
