/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'uiComponent',
    'Magento_MediaGalleryUi/js/action/deleteImage',
    'Magento_MediaGalleryUi/js/action/getDetails',
    'Magento_MediaGalleryUi/js/grid/columns/image/insertImageAction'
], function ($, _, Component, deleteImage, getDetails, image) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento_MediaGalleryUi/grid/columns/image/actions',
            mediaGalleryImageDetailsName: 'mediaGalleryImageDetails',
            actionsList: [
                {
                    name: 'image-details',
                    title: $.mage.__('View Details'),
                    handler: 'viewImageDetails'
                },
                {
                    name: 'delete',
                    title: $.mage.__('Delete'),
                    handler: 'deleteImageAction'
                }
            ],
            modules: {
                imageModel: '${ $.imageModelName }',
                mediaGalleryImageDetails: '${ $.mediaGalleryImageDetailsName }'
            }
        },

        /**
         * Initialize the component
         *
         * @returns {Object}
         */
        initialize: function () {
            this._super();
            this.initEvents();

            return this;
        },

        /**
         * Initialize image action events
         */
        initEvents: function () {
            $(this.imageModel().addSelectedBtnSelector).click(function () {
                image.insertImage(
                   this.imageModel().getSelected(),
                    {
                        onInsertUrl: this.imageModel().onInsertUrl,
                        storeId: this.imageModel().storeId
                    }
                );
            }.bind(this));
            $(this.imageModel().deleteSelectedBtnSelector).click(function () {
                this.deleteImageAction(this.imageModel().selected());
            }.bind(this));

        },

        /**
         * Delete image action
         *
         * @param {Object} record
         */
        deleteImageAction: function (record) {
            var imageDetailsUrl = this.imageModel().imageDetailsurl,
                deleteImageUrl = this.imageModel().deleteImageUrl,
                defaultConfirmationContent = $.mage.__('Are you sure you want to delete "' + record.path + '" image?');

            getDetails(imageDetailsUrl, record.id).then(function(imageDetails) {
                var confirmationContent = this.getConfirmationContentByImageDetails(imageDetails)
                    .concat(defaultConfirmationContent);
                deleteImage.deleteImageAction(record, deleteImageUrl, confirmationContent);
            }.bind(this)).fail(function () {
                deleteImage.deleteImageAction(record, deleteImageUrl, defaultConfirmationContent);
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

            if(_.isObject(details) && !_.isUndefined(details['6'])) {
                var detailValue = details['6'].value;

                return $.mage.__(this.getRecordRelatedContentHtml(detailValue));
            }

            return '';
        },

        /**
         * Get information about image use
         *
         * @param {Object|String} value
         * @return {String}
         */
        getRecordRelatedContentHtml: function (value) {
            var usedIn = 'This image is used in ';

            if (_.isObject(value) && !_.isEmpty(value)) {
                _.each(value, function (numberOfTimeUsed, moduleName) {
                    usedIn += numberOfTimeUsed + ' ' + moduleName + ' ';
                });

                return usedIn + '. ';
            } else {

                return 'This image is not used anywhere. ';
            }

            return value;
        },

        /**
         * View image details
         *
         * @param {Object} record
         */
        viewImageDetails: function (record) {
            var recordId = this.imageModel().getId(record);

            this.mediaGalleryImageDetails().showImageDetailsById(recordId);
        }
    });
});
