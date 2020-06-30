/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'Magento_MediaGalleryUi/js/action/getDetails',
    'Magento_MediaGalleryUi/js/action/deleteImages'
], function ($, _, getDetails, deleteImages) {
    'use strict';

    return {

        categoryContentType: 'Category',

        /**
         * Get information about image use
         *
         * @param {Array} recordsIds
         * @param {String} imageDetailsUrl
         * @param {String} deleteImageUrl
         */
        deleteImageAction: function (recordsIds, imageDetailsUrl, deleteImageUrl) {
            var imagesCount = Object.keys(recordsIds).length,
                confirmationContent = $.mage.__('%1 Are you sure you want to delete "%2" image%3?')
                .replace('%2', Object.keys(recordsIds).length).replace('%3', imagesCount > 1 ? 's' : ''),
                deferred = $.Deferred();

            getDetails(imageDetailsUrl, recordsIds)
                .then(function (imageDetails) {
                        confirmationContent = confirmationContent.replace(
                            '%1',
                            this.getRecordRelatedContentMessage(imageDetails)
                        );
                    }.bind(this)).fail(function () {
                confirmationContent = confirmationContent.replace('%1', '');
            }).always(function () {
                deleteImages(recordsIds, deleteImageUrl, confirmationContent).then(function (status) {
                    deferred.resolve(status);
                }).fail(function (message) {
                    deferred.reject(message);
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
            var usedInMessage = $.mage.__('This image%p is used in %s.'),
                usedIn = {},
                message = '',
                prefix = Object.keys(imageDetails).length  > 1 ? 's' : '';

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
                message +=  count + ' ' +  this.getEntityNameWithPrefix(entityName, count) +  ', ';
            }.bind(this));

            message = message.replace(/,\s*$/, '');
            message = usedInMessage.replace('%s', message).replace('%p', prefix);

            return message;
        },

        /**
         * Return entity name based on used in count
         *
         * @param {String} entityName
         * @param {String} count
         */
        getEntityNameWithPrefix: function (entityName, count) {
            var name;

            if (count > 1) {
                if (entityName === this.categoryContentType) {
                    name = entityName.slice(0, -1) + 'ies';
                } else {
                    name = entityName + 's';
                }

                return name;
            }

            return entityName;
        }
    };
});
