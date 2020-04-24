/**
 * Copyright © Magento, Inc. All rights reserved.g
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'uiComponent',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/modal/alert',
    'underscore',
    'Magento_Ui/js/modal/prompt',
    'validation'
], function ($, Component, confirm, uiAlert, _, prompt) {
    'use strict';

    return Component.extend({
        defaults: {
            directoryTreeSelector: '#media-gallery-directory-tree',
            deleteButtonSelector: '#delete_folder',
            createFolderButtonSelector: '#create_folder',
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
            $(this.deleteButtonSelector).on('delete_folder', function () {
                this.getComfirmationPopupDeleteFolder();
            }.bind(this));

            $(this.createFolderButtonSelector).on('create_folder', function () {
                this.getPrompt({
                    title: 'New Folder Name:',
                    content: '',
                    actions: {
                        /**
                         * Confirm action
                         */
                        confirm: function (folderName) {
                            this.createFolder(folderName, this.selectedFolder());
                        }.bind(this)
                    },
                    buttons: [{
                        text: $.mage.__('Cancel'),
                        class: 'action-secondary action-dismiss',

                        /**
                         * Close modal
                         */
                        click: function () {
                            this.closeModal();
                        }
                    }, {
                        text: $.mage.__('Confirm'),
                        class: 'action-primary action-accept'
                    }]
                });
            }.bind(this));
        },

        /**
         * Send post request by provided params
         *
         * @param {String} url
         * @param {Object} data
         * @param {String} errorMessage
         * @param {Callback} succesCallback
         */
        sendPostRequest: function (url, data, errorMessage, succesCallback) {
            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                showLoader: true,
                data: data,
                context: this,
                success: succesCallback,

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
                        message = errorMessage;
                    } else {
                        message = response.responseJSON.message;
                    }
                    uiAlert({
                        content: message
                    });
                }
            });

        },

        /**
         * Create folder by provided path
         *
         * @param {String} path
         * @param {String} subPath
         */
        createFolder: function (path, subPath) {
            var folder = _.isUndefined(subPath) ? '/' : subPath,
                data = {
                    path: folder,
                    name: path
                },
                errorMessage = 'There was an error on attempt to create folder!',
                callback = function () {
                    this.directoryTree().reloadJsTree();
                }.bind(this);

            this.sendPostRequest(this.directoryTree().createDirectoryUrl, data, errorMessage, callback);
        },

        /**
          * Return configured prompt with input field
          */
        getPrompt: function (data) {
                prompt({
                    title: $.mage.__(data.title),
                    content:  $.mage.__(data.content),
                    modalClass: 'media-gallery-folder-prompt',
                    validation: true,
                    validationRules: ['required-entry', 'validate-alphanum'],
                    attributesField: {
                        name: 'folder_name',
                        'data-validate': '{required:true, validate-alphanum}',
                        maxlength: '128'
                    },
                    attributesForm: {
                        novalidate: 'novalidate',
                        action: ''
                    },
                    context: this,
                    actions: data.actions,
                    buttons: data.buttons
                });
            },

        /**
          * Confirmation popup for delete folder action.
          */
        getComfirmationPopupDeleteFolder: function () {
            confirm({
                title: $.mage.__('Are you sure you want to delete this folder?'),
                modalClass: 'delete-folder-confirmation-popup',
                content: $.mage.__('The following folder is going to be deleted: %1')
                    .replace('%1', this.selectedFolder()),
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
            var  data = {
                    path: path
                },
                errorMessage = 'There was an error on attempt to delete folder!',
                callback = function () {
                    this.directoryTree().removeNode();
                    this.directoryTree().selectStorageRoot();
                }.bind(this);

            this.sendPostRequest(this.directoryTree().deleteDirectoryUrl, data, errorMessage, callback);
        },

        /**
         * Set inactive all nodes, adds disable state to Delete Folder Button
         */
        setInActive: function () {
            this.selectedFolder(null);
            $(this.deleteButtonSelector).attr('disabled', true).addClass('disabled');
        },

        /**
         * Set active node, remove disable state from Delete Forlder button
         *
         * @param {String} folderId
         */
        setActive: function (folderId) {
            this.selectedFolder(folderId);
            $(this.deleteButtonSelector).removeAttr('disabled').removeClass('disabled');
        }
    });
});
