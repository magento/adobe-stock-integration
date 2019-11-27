<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model;

use Magento\AdobeStockAsset\Model\ResourceModel\Category as CategoryResourceModel;
use Magento\AdobeStockAssetApi\Api\Data\CategoryExtensionInterface;
use Magento\AdobeStockAssetApi\Api\Data\CategoryInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Representing the Adobe Stock asset category retrieved from Adobe Stock API (id matches to id on Adobe Stock side)
 */
class Category extends AbstractExtensibleModel implements CategoryInterface
{
    private const ID = 'id';
    private const NAME = 'name';

    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        $this->_init(CategoryResourceModel::class);
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
    public function getExtensionAttributes(): CategoryExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(CategoryExtensionInterface $extensionAttributes): void
    {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
