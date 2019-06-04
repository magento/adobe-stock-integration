<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdobeStockAsset\Model\Request;


/**
 * Class Config
 * @package Magento\AdobeStockAsset\Model\Request\Builder\Config
 */
class Config implements ConfigInterface
{
    /**
     * @var Config\Data
     */
    private $dataStorage;

    /**
     * Config constructor.
     * @param Config\Data $dataStorage
     */
    public function __construct(Config\Data $dataStorage)
    {
        $this->dataStorage = $dataStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestConfig(string $name) : array
    {
        $config = $this->dataStorage->get();
        return $config[$name] ?? null;
    }
}
