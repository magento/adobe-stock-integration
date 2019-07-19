<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

\Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize();

/** @var \Magento\TestFramework\ObjectManager $objectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();


/** @var $asset \Magento\AdobeStockAsset\Model\Asset */
$asset = $objectManager->create(\Magento\AdobeStockAsset\Model\Asset::class);

$asset->isObjectNew(true);
$asset->setAdobeId(1);
$asset->setIsLicensed(1);
$asset->setPreviewWidth(1);
$asset->setPreviewHeight(1);
$asset->setWidth(1);
$asset->setHeight(1);

/** @var \Magento\AdobeStockAsset\Model\AssetRepository $assetRepository */
$assetRepository = $objectManager->create(\Magento\AdobeStockAsset\Model\AssetRepository::class);
$assetRepository->save($asset);
