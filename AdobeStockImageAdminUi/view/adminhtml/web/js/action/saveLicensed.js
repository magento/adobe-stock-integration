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
            destinationPath;

        return new window.Promise(function (resolve, reject) {
            if (path !== '') {
                imageName = pathUtility.getImageNameFromPath(path);
                destinationPath = pathUtility.buildPath(directoryPath, imageName, contentType);
                saveAction(
                    requestUrl,
                    id,
                    destinationPath
                ).then(function () {
                    resolve(destinationPath);
                }).catch(function (message) {
                    reject(message);
                });

                return;
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
