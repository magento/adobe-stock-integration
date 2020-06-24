/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/modal/prompt',
    'text!Magento_AdobeStockImageAdminUi/template/modal/adobe-modal-prompt-content.html'
], function ($, prompt, adobePromptContentTmpl) {
    'use strict';

    return function (proposedfileName, fileExtension) {
        var deferred = $.Deferred(),
            data = {
                'title': $.mage.__('Save'),
                'content': $.mage.__('File Name'),
                'visible': true,
                'actions': {
                    /**
                     * Resolve with the specified file name
                     *
                     * @param {String} fileName
                     */
                    confirm: function (fileName) {
                        deferred.resolve(fileName);
                    }
                },
                'buttons': [
                    {
                        text: $.mage.__('Cancel'),
                        class: 'action-secondary action-dismiss',

                        /**
                         * Close modal on button click
                         */
                        click: function () {
                            this.closeModal();
                            deferred.reject();
                        }
                    },
                    {
                        text: $.mage.__('Confirm'),
                        class: 'action-primary action-accept'
                    }
                ]
            };

        prompt({
            title: data.title,
            content:  data.content,
            value: proposedfileName,
            imageExtension: fileExtension,
            visible: data.visible,
            promptContentTmpl: adobePromptContentTmpl,
            modalClass: 'adobe-stock-save-preview-prompt',
            validation: true,
            promptField: '[data-role="adobe-stock-image-name-field"]',
            validationRules: ['required-entry', 'validate-image-name'],
            attributesForm: {
                novalidate: 'novalidate',
                action: '',
                onkeydown: 'return event.key != \'Enter\';'
            },
            attributesField: {
                name: 'name',
                'data-validate': '{required:true}',
                maxlength: '128'
            },
            context: this,
            actions: data.actions,
            buttons: data.buttons
        });

        return deferred.promise();
    };
});
