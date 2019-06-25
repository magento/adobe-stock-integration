<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AdobeStockAsset\Model\Config\Backend;

/**
 * Class AdobeStockApi
 * @package Magento\AdobeStockAsset\Model\Config\Backend
 */
class AdobeStockApi extends \Magento\Config\Model\Config\Backend\Encrypted {

    /**
     * @var \Magento\AdobeStockClientApi\Api\ClientInterface
     */
    protected $_client;

    /**
     * AdobeStockApi constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\AdobeStockClientApi\Api\ClientInterface $client
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\AdobeStockClientApi\Api\ClientInterface $client,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->_client = $client;
        parent::__construct($context, $registry, $config, $cacheTypeList, $encryptor, $resource, $resourceCollection, $data);
    }

    public function beforeSave()
    {
        $value = $this->getValue();
        $this->_client->testConnection($value);
        parent::beforeSave(); // TODO: Change the autogenerated stub
    }
}
