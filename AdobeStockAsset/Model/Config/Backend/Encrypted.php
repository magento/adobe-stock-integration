<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\Config\Backend;

use AdobeStock\Api\Client\AdobeStock;
use Magento\AdobeStockClientApi\Api\ClientInterface;
use Magento\AdobeStockClient\Model\Config as AdobeStockConfig;
use Magento\AdobeStockClient\Model\ConnectionFactory;
use Magento\Framework\Exception\IntegrationException;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Psr\Log\LoggerInterface;

/**
 * Class Encrypted
 */
class Encrypted extends \Magento\Config\Model\Config\Backend\Encrypted
{
    /**
     * @var AdobeStockConfig
     */
    private $adobeStockConfig;

    /**
     * @var ConnectionFactory
     */
    private $connectionFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * Encrypted constructor.
     *
     * @param Context               $context
     * @param Registry              $registry
     * @param ScopeConfigInterface  $config
     * @param ConnectionFactory     $connectionFactory
     * @param LoggerInterface       $logger
     * @param AdobeStockConfig      $adobeStockConfig
     * @param ClientInterface       $client
     * @param TypeListInterface     $cacheTypeList
     * @param EncryptorInterface    $encryptor
     * @param AbstractResource|null $resource
     * @param AbstractDb|null       $resourceCollection
     * @param array                 $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        ConnectionFactory $connectionFactory,
        LoggerInterface $logger,
        AdobeStockConfig $adobeStockConfig,
        ClientInterface $client,
        TypeListInterface $cacheTypeList,
        EncryptorInterface $encryptor,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->adobeStockConfig = $adobeStockConfig;
        $this->connectionFactory = $connectionFactory;
        $this->logger = $logger;
        $this->client = $client;
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $encryptor,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @throws IntegrationException
     */
    public function beforeSave()
    {
        $this->_dataSaveAllowed = false;
        $value = (string)$this->getValue();
        if ($this->isAPiKeyValid($value)) {
            parent::beforeSave();
        } else {
            $this->setValue('');
            $message = __('API key is invalid and can not be saved. Please, check it and try again.');
            throw new IntegrationException($message);
        }
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    private function isAPiKeyValid(string $value): bool
    {
        try {
            $connectionInstance = $this->generateConnectionInstance($value);
            $isConnectionCreated = $this->client->testConnection($connectionInstance);

            return $isConnectionCreated;
        } catch (\Exception $exception) {
            $message = __(
                'Initialize test API KEY connection failed: %error_message',
                ['error_message' => $exception]
            );
            $this->logger->critical($message->render());
            return false;
        }
    }

    /**
     * @param string $apiKey
     *
     * @return AdobeStock
     */
    private function generateConnectionInstance(string $apiKey): AdobeStock
    {
        return $this->connectionFactory->create(
            $apiKey,
            $this->adobeStockConfig->getProductName(),
            $this->adobeStockConfig->getTargetEnvironment()
        );
    }
}
