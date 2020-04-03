/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'uiComponent',
    'Magento_Ui/js/modal/confirm',
    'Magento_MediaGalleryUi/js/grid/columns/image/insertImageAction'
], function ($, _, Component, confirmation, image) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento_MediaGalleryUi/grid/columns/image/actions',
            deleteImageUrl: 'media_gallery/image/delete',
            mediaGalleryImageDetailsName: 'mediaGalleryImageDetails',
            actionsList: [
                {
                    name: 'image-details',
                    title: 'View Details',
                    handler: 'viewImageDetails'
                },
                {
                    name: 'delete',
                    title: 'Delete',
                    handler: 'deleteImageAction'
                }
            ],
            modules: {
                imageModel: '${ $.imageModelName }',
                messages: '${ $.messagesName }',
                provider: '${ $.providerName }',
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
            var baseContent = $.mage.__('Are you sure you want to delete "%s" image?'),
                title = $.mage.__('Delete image'),
                cancelText = $.mage.__('Cancel'),
                deleteImageText = $.mage.__('Delete Image'),
                deleteImageCallback = this.deleteImage.bind(this);

            confirmation({
                title: title,
                content: baseContent.replace('%s', record.name),
                buttons: [
                    {
                        text: cancelText,
                        class: 'action-secondary action-dismiss',

                        /**
                         * Close modal
                         */
                        click: function () {
                            this.closeModal();
                        }
                    },
                    {
                        text: deleteImageText,
                        class: 'action-primary action-accept',

                        /**
                         * Delete Image and close modal
                         */
                        click: function () {
                            deleteImageCallback(record);
                            this.closeModal();
                        }
                    }
                ]
            });
        },

        /**
         * View image details
         *
         * @param {Object} record
         */
        viewImageDetails: function (record) {
            var recordId = this.imageModel().getId(record);

            this.mediaGalleryImageDetails().showImageDetailsById(recordId);
        },

        /**
         * Delete image
         *
         * @param {Object} record
         */
        deleteImage: function (record) {
            var recordId = this.imageModel().getId(record);

            $.ajax({
                type: 'POST',
                url: this.imageModel().deleteImageUrl,
                dataType: 'json',
                showLoader: true,
                data: {
                    'image_id': recordId
                },
                context: this,

                /**
                 * Success handler for deleting image
                 *
                 * @param {Object} response
                 */
                success: function (response) {
                    var message = !_.isUndefined(response.message) ? response.message : null;

                    if (!response.success) {
                        message = message || $.mage.__('There was an error on attempt to delete the image.');
                        this.addMessage('error', message);

                        return;
                    }

                    message = message || $.mage.__('You have successfully removed the image.');
                    this.reloadGrid();
                    this.addMessage('success', message);
                    $(this.imageModel().deleteSelectedBtnSelector).addClass('no-display');
                    $(this.imageModel().addSelectedBtnSelector).addClass('no-display');
                }.bind(this),

                /**
                 * Error handler for deleting image
                 *
                 * @param {Object} response
                 */
                error: function (response) {
                    var message;

                    if (typeof response.responseJSON === 'undefined' ||
                        typeof response.responseJSON.message === 'undefined'
                    ) {
                        message = 'There was an error on attempt to delete the image.';
                    } else {
                        message = response.responseJSON.message;
                    }

                    this.addMessage('error', message);
                }.bind(this)
            });
        },

        /**
         * Reload grid
         */
        reloadGrid: function () {
            var provider = this.provider(),
                dataStorage = provider.storage();

            dataStorage.clearRequests();
            provider.reload();
        },

        /**
         * Add message
         *
         * @param {String} code
         * @param {String} message
         */
        addMessage: function (code, message) {
            this.messages().add(code, message);
            this.messages().scheduleCleanup();
        }
    });
});
