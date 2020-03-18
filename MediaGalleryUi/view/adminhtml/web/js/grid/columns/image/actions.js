/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'uiComponent',
    'Magento_Ui/js/modal/confirm'
], function ($, _, Component, confirmation) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento_MediaGalleryUi/grid/columns/image/actions',
            deleteImageUrl: 'media_gallery/image/delete',
            actionsList: [
                {
                    name: 'delete',
                    title: 'Delete',
                    handler: 'deleteImageAction'
                }
            ],
            modules: {
                imageModel: '${ $.imageModelName }',
                messages: '${ $.messagesName }',
                provider: '${ $.providerName }'
            }
        },

        /**
         * Initialize the component
         *
         * @returns {Object}
         */
        initialize: function () {
            this._super();
            $(this.imageModel().addSelectedBtnSelector).click(function () {
                this.insertImage();
            }.bind(this));

            return this;
        },

        /**
         * Insert selected image
         *
         * @returns {Boolean}
         */
        insertImage: function () {
            var record = this.imageModel().getSelected(),
                targetElement;

            if (record === null) {
                return false;
            }
            targetElement = this.getTargetElement();

            if (!targetElement.length) {
                window.MediabrowserUtility.closeDialog();
                throw 'Target element not found for content update';
            }
            targetElement.val(record['thumbnail_url'])
                .data('mime-type', record['content_type'])
                .trigger('change');
            window.MediabrowserUtility.closeDialog();
            targetElement.focus();
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
        },

        /**
         * Get target element
         *
         * @returns {*|n.fn.init|jQuery|HTMLElement}
         */
        getTargetElement: function () {
            return $('#' + this.imageModel().targetElementId);
        }
    });
});
