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
        return new window.Promise(function (resolve, reject) {
            saveConfirmation(
                pathUtility.generateImageName(title, id),
                pathUtility.getImageExtension(contentType)
            ).then(function (fileName) {
                var destinationPath = pathUtility.buildPath(directoryPath, fileName, contentType);

                saveAction(
                    requestUrl,
                    id,
                    destinationPath
                ).then(function () {
                    resolve(destinationPath);
                }).catch(function (error) {
                    reject(error);
                });
            }).catch(function (error) {
                reject(error);
            });
        });
    };
});
