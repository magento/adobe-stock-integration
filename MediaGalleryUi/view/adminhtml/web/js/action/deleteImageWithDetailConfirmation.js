/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'Magento_MediaGalleryUi/js/action/getDetails',
    'Magento_MediaGalleryUi/js/action/deleteImage'
], function ($, _, getDetails, deleteImage) {
    'use strict';

    return {

        /**
         * Get information about image use
         *
         * @param {Object} record
         * @param {String} imageDetailsUrl
         * @param {String} deleteImageUrl
         */
        deleteImageAction: function (record, imageDetailsUrl, deleteImageUrl) {
            var confirmationContent = $.mage.__('%1 Are you sure you want to delete "%2" image?')
                .replace('%2', record.path);

            getDetails(imageDetailsUrl, record.id)
                .then(function (imageDetails) {
                    confirmationContent = confirmationContent.replace(
                        '%1',
                        this.getConfirmationContentByImageDetails(imageDetails)
                    );
                }.bind(this)).fail(function () {
                confirmationContent = confirmationContent.replace('%1', '');
            }).always(function () {
                deleteImage([record], deleteImageUrl, confirmationContent);
            });
        },

        /**
         * Returns confirmation content with information about related content
         *
         * @param {Object} imageDetails
         * @return String
         */
        getConfirmationContentByImageDetails: function (imageDetails) {
            var details = imageDetails.details;

            if (_.isObject(details) && !_.isUndefined(details['6'])) {
                return this.getRecordRelatedContentMessage(details['6'].value);
            }

            return '';
        },

        /**
         * Get information about image use
         *
         * @param {Object|String} value
         * @return {String}
         */
        getRecordRelatedContentMessage: function (value) {
            var usedInMessage = $.mage.__('This image is used in %s.'),
                usedIn = '';

            if (_.isObject(value) && !_.isEmpty(value)) {
                _.each(value, function (numberOfTimeUsed, moduleName) {
                    usedIn += numberOfTimeUsed + ' ' + moduleName + ', ';
                });
                usedIn = usedIn.replace(/,\s*$/, '');

                return usedInMessage.replace('%s', usedIn);
            }

            return '';
        }
    };
});
