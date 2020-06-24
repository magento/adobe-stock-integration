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
        var deferred = $.Deferred(),
            destinationPath;

        licenseConfirmation(
                title,
                quotaMessage,
                isDownloaded,
                pathUtility.generateImageName(title, id),
                pathUtility.getImageExtension(contentType)
            ).then(function (fileName) {

                if (typeof fileName === 'undefined') {
                    fileName = pathUtility.getImageNameFromPath(path);
                }

                destinationPath = pathUtility.buildPath(directoryPath, fileName, contentType);

                saveAction(
                    requestUrl,
                    id,
                    destinationPath
                ).then(function () {
                    deferred.resolve(destinationPath);
                }).fail(function (message) {
                    deferred.reject(message);
                });
            }).fail(function (error) {
                deferred.reject(error);
            });

        return deferred.promise();
    };
});
