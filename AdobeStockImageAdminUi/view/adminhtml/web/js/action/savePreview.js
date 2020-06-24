/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'Magento_AdobeStockImageAdminUi/js/action/save',
    'Magento_AdobeStockImageAdminUi/js/confirmation/save',
    'Magento_AdobeStockImageAdminUi/js/path-utility'
], function ($, saveAction, saveConfirmation, pathUtility) {
    'use strict';

    return function (requestUrl, id, title, contentType, directoryPath) {
        var deferred = $.Deferred(),
            destinationPath;

        saveConfirmation(
            pathUtility.generateImageName(title, id),
            pathUtility.getImageExtension(contentType)
        ).then(function (fileName) {
            destinationPath = pathUtility.buildPath(directoryPath, fileName, contentType);

            saveAction(
                requestUrl,
                id,
                destinationPath
            ).then(function () {
                deferred.resolve(destinationPath);
            }).fail(function (error) {
                deferred.reject(error);
            });
        }).fail(function (error) {
            deferred.reject(error);
        });

        return deferred.promise();
    };
});
