/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'uiComponent',
    'Magento_Ui/js/modal/confirm',
    'wysiwygAdapter'
], function ($, _, Component, confirmation, wysiwyg) {
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
                this.insertImage();
            }.bind(this));
            $(this.imageModel().deleteSelectedBtnSelector).click(function () {
                this.deleteImageAction(this.imageModel().selected());
            }.bind(this));

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

            if (targetElement.is('textarea')) {
                $.ajax({
                    url: this.imageModel().onInsertUrl,
                    data: {
                        filename: record['encoded_id'],
                        'store_id': this.imageModel().storeId,
                        'as_is': 1,
                        'force_static_path': targetElement.data('force_static_path') ? 1 : 0,
                        'form_key': FORM_KEY
                    },
                    context: this,
                    showLoader: true
                }).done($.proxy(function (data) {
                    $.mage.mediabrowser().insertAtCursor(targetElement.get(0), data);
                }, this));
            } else {
                targetElement.val(record['thumbnail_url'])
                    .data('size', record.size)
                    .data('mime-type', record['content_type'])
                    .trigger('change');
            }
            window.MediabrowserUtility.closeDialog();
            targetElement.focus();

        },

        /**
         * Return opener Window object if it exists, not closed and editor is active
         *
         * return {Object|null}
         */
        getMediaBrowserOpener: function () {
            if (typeof wysiwyg != 'undefined' &&
                wysiwyg.get(this.imageModel().targetElementId) &&
                typeof tinyMceEditors != 'undefined' &&
                !tinyMceEditors.get(this.imageModel().targetElementId).getMediaBrowserOpener().closed
            ) {
                return tinyMceEditors.get(this.imageModel().targetElementId).getMediaBrowserOpener();
            }

            return null;
        },

        /**
         * Get target element
         *
         * @returns {*|n.fn.init|jQuery|HTMLElement}
         */
        getTargetElement: function () {
            var opener, targetElementId;

            if (typeof wysiwyg != 'undefined' && wysiwyg.get(this.imageModel().targetElementId)) {
                opener = this.getMediaBrowserOpener() || window;
                targetElementId = tinyMceEditors.get(this.imageModel().targetElementId).getMediaBrowserTargetElementId();

                return $(opener.document.getElementById(targetElementId));
            }

            return $('#' + this.imageModel().targetElementId);
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
