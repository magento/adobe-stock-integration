/**
 * Copyright © Magento, Inc. All rights reserved.g
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'uiComponent',
    'Magento_Ui/js/modal/confirm'
], function ($, Component, confirm) {
    'use strict';

    return Component.extend({
        defaults: {
            directoryTreeSelector: '#media-gallery-directory-tree',
            deleteButtonSelector: '#delete_folder',
            messageDelay: 5,
            messagesName: 'media_gallery_listing.media_gallery_listing.messages',
            modules: {
                directoryTree: '${ $.parentName }.media_gallery_directories',
                messages: '${ $.messagesName }'
            }
        },

        /**
         * Initializes media gallery directories component.
         *
         * @returns {Sticky} Chainable.
         */
        initialize: function () {
            this._super().observe(['selectedFolder', 'activeNodeId']);
            this.initEvents();

            return this;
        },

        /**
          * Initialize directories events
          */
        initEvents: function () {
            $(this.deleteButtonSelector).on('delete_folder', function () {
                this.deleteFolderComfirmationPopup();
            }.bind(this));
        },

        /**
          * Confirmation popup for delete folder action.
          */
        deleteFolderComfirmationPopup: function () {
            confirm({
                title: $.mage.__('Are you sure you want to delete ?'),
                content: 'Are you sure you want to delete folder: ' + this.selectedFolder(),
                actions: {

                    /**
                     * Delete folder on button click
                     */
                    confirm: function () {
                        this.deleteFolder(this.selectedFolder());
                    }.bind(this)
                }
            });
        },

        /**
          * Delete folder action
          *
          * @param {String} path
          */
        deleteFolder: function (path) {
            $.ajax({
                type: 'POST',
                url: this.directoryTree().deleteDirectoryUrl,
                dataType: 'json',
                showLoader: true,
                data: {
                    'path': path
                },
                context: this,

                /**
                 * Success handler for Delete folder action
                 *
                 */
                success: function () {
                    this.directoryTree().removeNode(this.activeNodeId());
                },

                /**
                 * Error handler for Delete folder action
                 *
                 * @param {Object} response
                 */
                error: function (response) {
                    var message;

                    if (typeof response.responseJSON === 'undefined' ||
                        response.responseJSON.success === 'false'
                    ) {
                        message = 'There was an error on attempt to delete folder!';
                    } else {
                        message = response.responseJSON.message;
                    }
                    this.messages().add('error', message);
                    this.messages().scheduleCleanup(this.messageDelay);
                }
            });
        },

        /**
         * Set active node, remove disable state from Delete Forlder button
         *
         * @param {String} folderId
         * @param {String} nodeId
         */
        setActive: function (folderId, nodeId) {
            this.selectedFolder(folderId);
            this.activeNodeId(nodeId);
            $(this.deleteButtonSelector).removeAttr('disabled').removeClass('disabled');
        }
    });
});
