/**
 * Copyright © Magento, Inc. All rights reserved.g
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'uiComponent'
], function ($, Component) {
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
            this._super().observe(['selectedFolder']);
            this.initEvents();

            return this;
        },

        /**
          * Initialize directories events
          */
        initEvents: function () {
            $(this.deleteButtonSelector).on('delete_folder', function (path) {
                this.deleteFolder(this.selectedFolder());
            }.bind(this));
        },

        /**
          * Delete folder action
          *
          * @param {String} path
          * @param {Integer} nodeId
          */
        deleteFolder: function (path, nodeId) {
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
                    this.directoryTree().removeNode(nodeId);
                },

                /**
                 * Error handler for Delete folder action
                 *
                 * @param {Object} response
                 */
                error: function (response) {
                    var message;

                    if (typeof response.responseJSON === 'undefined' ||
                        typeof response.responseJSON.success === 'false'
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
         * Set avtive delete button for selected folder
         *
         * @param {String} folderId
         */
        setActive: function (folderId) {
            this.selectedFolder(folderId);
            $(this.deleteButtonSelector).removeAttr('disabled').removeClass('disabled');
        }
    });
});
