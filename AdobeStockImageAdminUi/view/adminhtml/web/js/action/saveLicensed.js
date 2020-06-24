/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'Magento_AdobeStockImageAdminUi/js/action/save',
    'Magento_AdobeStockImageAdminUi/js/confirmation/saveLicensed',
    'Magento_AdobeStockImageAdminUi/js/path-utility'
], function ($, saveAction, saveLicensedConfirmation, pathUtility) {
    'use strict';

    return function (requestUrl, id, title, path, contentType, directoryPath) {
        var imageName = '',
            destinationPath,
            deferred = $.Deferred();

        if (path !== '') {
            imageName = pathUtility.getImageNameFromPath(path);
            destinationPath = pathUtility.buildPath(directoryPath, imageName, contentType);
            saveAction(
                requestUrl,
                id,
                destinationPath
            ).then(function () {
                deferred.resolve(destinationPath);
            }).fail(function (message) {
                deferred.reject(message);
            });

            return deferred.promise();
        }

        saveLicensedConfirmation(
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
            }).fail(function (message) {
                deferred.reject(message);
            });
        }).fail(function (error) {
            deferred.reject(error);
        });

        return deferred.promise();
    };
});
