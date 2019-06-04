<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdobeStockAsset\Model\Request;

use Magento\Framework\Config\DataInterface;

/**
 * Class Config
 */
class Config implements ConfigInterface
{
    /**
     * @var DataInterface
     */
    private $dataStorage;

    /**
     * Config constructor.
     * @param DataInterface $dataStorage
     */
    public function __construct(DataInterface $dataStorage)
    {
        $this->dataStorage = $dataStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestConfig(string $name) : array
    {
        return $this->dataStorage->get($name);
    }
}
