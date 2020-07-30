/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'Magento_MediaGalleryUi/js/action/getDetails',
    'Magento_MediaGalleryUi/js/action/deleteImages',
    'mage/translate'
], function ($, _, getDetails, deleteImages, $t) {
    'use strict';

    return {

        /**
         * Get information about image use
         *
         * @param {Array} recordsIds
         * @param {String} imageDetailsUrl
         * @param {String} deleteImageUrl
         */
        deleteImageAction: function (recordsIds, imageDetailsUrl, deleteImageUrl) {
            var imagesCount = Object.keys(recordsIds).length,
                confirmationContent = $t('%1 Are you sure you want to delete "%2" image%3?')
                .replace('%2', Object.keys(recordsIds).length).replace('%3', imagesCount > 1 ? 's' : ''),
                deferred = $.Deferred();

            getDetails(imageDetailsUrl, recordsIds)
                .then(function (response) {
                        confirmationContent = confirmationContent.replace(
                            '%1',
                            this.getRecordRelatedContentMessage(response.imageDetails)
                        );
                    }.bind(this)).fail(function () {
                confirmationContent = confirmationContent.replace('%1', '');
            }).always(function () {
                deleteImages(recordsIds, deleteImageUrl, confirmationContent).then(function (status) {
                    deferred.resolve(status);
                }).fail(function (jqXHR) {
                    deferred.reject(JSON.parse(jqXHR.responseText).message);
                });
            });

            return deferred.promise();
        },

        /**
         * Get information about image use
         *
         * @param {Object|String} imageDetails
         * @return {String}
         */
        getRecordRelatedContentMessage: function (imageDetails) {
            var usedInMessage = $t('The selected assets are used for the following entities content: '),
                usedIn = {};

            $.each(imageDetails, function (key, image) {
                if (_.isObject(image.details[6]) && !_.isEmpty(image.details[6].value)) {
                    $.each(image.details[6].value, function (entityName, count) {
                        usedIn[entityName] =  usedIn[entityName] + count || count;
                    });
                }
            });

            if (_.isEmpty(usedIn)) {
                return '';
            }
            $.each(usedIn, function (entityName, count) {
                usedInMessage +=  entityName +  '(' + count + '), ';
            });

            return usedInMessage;
        }
    };
});
