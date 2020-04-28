/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'Magento_AdobeStockImageAdminUi/js/action/save',
    'Magento_AdobeStockImageAdminUi/js/confirmation/license',
    'Magento_AdobeStockImageAdminUi/js/path-utility'
], function ($, saveAction, licenseConfirmation, pathUtility) {
    'use strict';

    return function (
        requestUrl,
        id,
        title,
        path,
        contentType,
        isDownloaded,
        quotaMessage,
        directoryPath
    ) {
        return new window.Promise(function (resolve, reject) {
            licenseConfirmation(
                title,
                quotaMessage,
                isDownloaded,
                pathUtility.generateImageName(title, id),
                pathUtility.getImageExtension(contentType)
            ).then(function (fileName) {
                var destinationPath;

                if (typeof fileName === 'undefined') {
                    fileName = pathUtility.getImageNameFromPath(path);
                }

                destinationPath = pathUtility.buildPath(directoryPath, fileName, contentType);

                saveAction(
                    requestUrl,
                    id,
                    destinationPath
                ).then(function () {
                    resolve(destinationPath);
                }).catch(function (message) {
                    reject(message);
                });
            }).catch(function (error) {
                reject(error);
            });
        });
    };
});
