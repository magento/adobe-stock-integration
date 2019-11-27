<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAsset\Model\ResourceModel\Creator as CreatorResourceModel;
use Magento\AdobeStockAssetApi\Api\Data\CreatorExtensionInterface;
use Magento\AdobeStockAssetApi\Api\Data\CreatorInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Representing the Adobe Stock asset creator (person who uploaded the asset to the Adobe Stock)
 */
class Creator extends AbstractExtensibleModel implements CreatorInterface
{
    private const ID = 'id';
    private const NAME = 'name';

    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        $this->_init(CreatorResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function getId(): ?int
    {
        $id = $this->getData(self::ID);

        if (!$id) {
            return null;
        }

        return (int) $id;
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return (string) $this->getData(self::NAME);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes(): CreatorExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(CreatorExtensionInterface $extensionAttributes): void
    {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
