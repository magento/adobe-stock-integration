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
            var imageDetailsUrl = this.mediaGalleryImageDetails().imageDetailsUrl,
                deleteImageUrl = this.imageModel().deleteImageUrl,
                confirmationContent = $.mage.__('%1 Are you sure you want to delete "%2" image?')
                    .replace('%2', record.path);

            getDetails(imageDetailsUrl, record.id)
                .then(function (imageDetails) {
                    confirmationContent = confirmationContent.replace(
                        '%1',
                        this.getConfirmationContentByImageDetails(imageDetails)
                    );
                }.bind(this)).fail(function () {
                confirmationContent = confirmationContent.replace('%1', "");
            }).always(function () {
                deleteImage.deleteImageAction(record, deleteImageUrl, confirmationContent);
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
                var detailValue = details['6'].value;

                return this.getRecordRelatedContentMessage(detailValue);
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
                usedIn = usedIn.replace(/,\s*$/, "");

                return  usedInMessage.replace('%s', usedIn);
            }
            return '';
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
